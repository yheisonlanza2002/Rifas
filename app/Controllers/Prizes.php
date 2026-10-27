<?php

namespace App\Controllers;

use App\Models\PrizeModel;
use App\Models\RaffleModel;
use App\Models\LotteryModel;

class Prizes extends BaseController
{
    protected PrizeModel $prizeModel;
    protected RaffleModel $raffleModel;
    protected LotteryModel $lotteryModel;

    private const STATUSES = [
        'ACTIVE'   => 'Activo',
        'INACTIVE' => 'Inactivo',
    ];

    private const CURRENCIES = [
        'COP' => 'Peso Colombiano (COP)',
        'USD' => 'Dólar Estadounidense (USD)',
        'EUR' => 'Euro (EUR)',
    ];

    private const PRIZE_TYPES = [
        'PRIZE' => 'Premio Físico',
        'MONEY' => 'Premio en Dinero',
    ];

    public function __construct()
    {
        $this->prizeModel = new PrizeModel();
        $this->raffleModel = new RaffleModel();
        $this->lotteryModel = new LotteryModel();
    }

    public function index(): string
    {
        $companyId = (int) session()->get('company_id');

        $prizes = $this->prizeModel
            ->forCompany($companyId)
            ->orderBy('prizes.position', 'ASC')
            ->findAll();

        return view('pages/prizes/index', [
            'title'      => 'Premios - Rifas',
            'prizes'     => $prizes,
            'statuses'   => self::STATUSES,
            'currencies' => self::CURRENCIES,
            'prizeTypes' => self::PRIZE_TYPES,
        ]);
    }

    public function create(): string
    {
        $companyId = (int) session()->get('company_id');

        return view('pages/prizes/create', [
            'title'      => 'Crear Premio - Rifas',
            'raffles'    => $this->getRaffles(),
            'lotteries'  => $this->lotteryModel->forCompany($companyId)->findAll(),
            'statuses'   => self::STATUSES,
            'currencies' => self::CURRENCIES,
            'prizeTypes' => self::PRIZE_TYPES,
        ]);
    }

    public function store()
    {
        if (!$this->validateData($this->request->getPost(), 'prizeCreate')) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('error', 'Revisa los errores del formulario.');
        }

        $post = $this->request->getPost();
        $prizeImage = $this->procesarImagenPremio();

        $prizeId = $this->prizeModel->insert([
            'uuid'            => $this->generateUuid(),
            'raffle_id'       => $post['raffle_id'],
            'lottery_id'      => $post['lottery_id'],
            'name'            => $post['name'],
            'description'     => $post['description'] ?? null,
            'position'        => $post['position'],
            'quantity'        => $post['quantity'],
            'value'           => !empty($post['value']) ? $post['value'] : null,
            'currency'        => $post['currency'],
            'image'           => $post['image'] ?? null,
            'odds'            => null,
            'status'          => $post['status'],
            'prize_type'      => $post['prize_type'],
            'recurrence_days' => $post['prize_type'] === 'MONEY' && !empty($post['recurrence_days']) ? $post['recurrence_days'] : null,
            'draw_date'       => $post['prize_type'] === 'MONEY' && !empty($post['draw_date']) ? $post['draw_date'] : null,
            'prize_image'     => $prizeImage,
        ]);

        $this->guardarPremiosAdicionales($post['raffle_id'], $post['position'], $post['additional_prizes'] ?? []);

        return redirect()->to(base_url('premios'))
            ->with('success', 'Premio creado correctamente.');
    }

    public function edit(int $id)
    {
        $prize = $this->findPrizeOrFail($id);
        if (!is_array($prize)) {
            return $prize;
        }

        $companyId = (int) session()->get('company_id');

        return view('pages/prizes/edit', [
            'title'      => 'Editar Premio - Rifas',
            'prize'      => $prize,
            'raffles'    => $this->getRaffles(),
            'lotteries'  => $this->lotteryModel->forCompany($companyId)->findAll(),
            'statuses'   => self::STATUSES,
            'currencies' => self::CURRENCIES,
            'prizeTypes' => self::PRIZE_TYPES,
        ]);
    }

    public function update(int $id)
    {
        $prize = $this->findPrizeOrFail($id);
        if (!is_array($prize)) {
            return $prize;
        }

        $data = $this->request->getPost();
        $data['id'] = $id;

        if (!$this->validateData($data, 'prizeUpdate')) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('error', 'Revisa los errores del formulario.');
        }

        $post = $this->request->getPost();
        $prizeImage = $this->procesarImagenPremio();

        $updateData = [
            'raffle_id'       => $post['raffle_id'],
            'lottery_id'      => $post['lottery_id'],
            'name'            => $post['name'],
            'description'     => $post['description'] ?? null,
            'position'        => $post['position'],
            'quantity'        => $post['quantity'],
            'value'           => !empty($post['value']) ? $post['value'] : null,
            'currency'        => $post['currency'],
            'image'           => $post['image'] ?? null,
            'status'          => $post['status'],
            'prize_type'      => $post['prize_type'],
            'recurrence_days' => $post['prize_type'] === 'MONEY' && !empty($post['recurrence_days']) ? $post['recurrence_days'] : null,
            'draw_date'       => $post['prize_type'] === 'MONEY' && !empty($post['draw_date']) ? $post['draw_date'] : null,
        ];

        if ($prizeImage) {
            $updateData['prize_image'] = $prizeImage;
        }

        $this->prizeModel->update($id, $updateData);

        return redirect()->to(base_url('premios'))
            ->with('success', 'Premio actualizado correctamente.');
    }

    public function delete(int $id)
    {
        $prize = $this->findPrizeOrFail($id);
        if (!is_array($prize)) {
            return $prize;
        }

        $this->prizeModel->delete($id);

        return redirect()->to(base_url('premios'))
            ->with('success', 'Premio eliminado correctamente.');
    }

    private function guardarPremiosAdicionales(int $raffleId, int $positionBase, array $prizes): void
    {
        if (empty($prizes)) return;

        $position = $positionBase + 1;

        foreach ($prizes as $p) {
            if (empty($p['name'])) continue;

            $imagePath = null;
            if (!empty($p['image']['tmp_name'])) {
                $file = $p['image'];
                $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp']) && $file['size'] <= 2097152) {
                    $nombre = uniqid('prize_') . '.' . $extension;
                    move_uploaded_file($file['tmp_name'], ROOTPATH . 'public/uploads/prizes/' . $nombre);
                    $imagePath = $nombre;
                }
            }

            $this->prizeModel->insert([
                'uuid'            => $this->generateUuid(),
                'raffle_id'       => $raffleId,
                'lottery_id'      => $p['lottery_id'] ?? null,
                'name'            => $p['name'],
                'description'     => null,
                'position'        => $position++,
                'quantity'        => 1,
                'value'           => $p['value'] ?? 0,
                'currency'        => 'COP',
                'image'           => null,
                'odds'            => null,
                'status'          => 'ACTIVE',
                'prize_type'      => $p['type'] ?? 'PRIZE',
                'recurrence_days' => ($p['type'] ?? 'PRIZE') === 'MONEY' && !empty($p['recurrence_days']) ? $p['recurrence_days'] : null,
                'draw_date'       => ($p['type'] ?? 'PRIZE') === 'MONEY' && !empty($p['draw_date']) ? $p['draw_date'] : null,
                'prize_image'     => $imagePath,
            ]);
        }
    }

    private function procesarImagenPremio(): ?string
    {
        $file = $this->request->getFile('prize_image');

        if (!$file || !$file->isValid()) {
            return null;
        }

        $extension = strtolower($file->getExtension());
        $permitidos = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($extension, $permitidos)) {
            return null;
        }

        if ($file->getSize() > 2097152) {
            return null;
        }

        $nombre = uniqid('prize_') . '.' . $extension;
        $file->move(ROOTPATH . 'public/uploads/prizes', $nombre);

        return $file->getName() === $nombre ? $nombre : $nombre;
    }

    private function getRaffles(): array
    {
        $companyId = (int) session()->get('company_id');

        return $this->raffleModel
            ->forCompany($companyId)
            ->where('status !=', 'CLOSED')
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    private function findPrizeOrFail(int $id)
    {
        $companyId = (int) session()->get('company_id');

        $prize = $this->prizeModel
            ->forCompany($companyId)
            ->withDeleted()
            ->find($id);

        if (!$prize || $prize['deleted_at'] !== null) {
            return redirect()->to(base_url('premios'))
                ->with('error', 'El premio no existe.');
        }

        return $prize;
    }

    private function generateUuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0x8000, 0xbfff), mt_rand(0x8000, 0xbfff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
