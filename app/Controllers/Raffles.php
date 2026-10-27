<?php

namespace App\Controllers;

use App\Models\RaffleModel;
use App\Models\TicketModel;
use App\Models\LotteryModel;

class Raffles extends BaseController
{
    protected RaffleModel $raffleModel;
    protected TicketModel $ticketModel;
    protected LotteryModel $lotteryModel;

    private const STATUSES = [
        'DRAFT'  => 'Borrador',
        'ACTIVE' => 'Activa',
        'CLOSED' => 'Cerrada',
    ];

    private const ASSIGNMENT_MODES = [
        'SEQUENTIAL' => 'Secuencial',
        'RANDOM'     => 'Aleatorio',
        'MANUAL'     => 'Manual',
    ];

    private const SELECTION_MODES = [
        'BOTH'        => 'Ambos',
        'SELECTION'   => 'Selección',
        'ASSIGNMENT'  => 'Asignación',
    ];

    private const CURRENCIES = [
        'COP' => 'Peso Colombiano (COP)',
        'USD' => 'Dólar Estadounidense (USD)',
        'EUR' => 'Euro (EUR)',
    ];

    public function __construct()
    {
        $this->raffleModel = new RaffleModel();
        $this->ticketModel = new TicketModel();
        $this->lotteryModel = new LotteryModel();
    }

    public function index(): string
    {
        $companyId = (int) session()->get('company_id');

        $raffles = $this->raffleModel
            ->forCompany($companyId)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('pages/raffles/index', [
            'title'             => 'Rifas - Rifas',
            'raffles'           => $raffles,
            'statuses'          => self::STATUSES,
            'assignmentModes'   => self::ASSIGNMENT_MODES,
            'selectionModes'    => self::SELECTION_MODES,
            'currencies'        => self::CURRENCIES,
        ]);
    }

    public function create(): string
    {
        $companyId = (int) session()->get('company_id');

        return view('pages/raffles/create', [
            'title'             => 'Crear Rifa - Rifas',
            'statuses'          => self::STATUSES,
            'assignmentModes'   => self::ASSIGNMENT_MODES,
            'selectionModes'    => self::SELECTION_MODES,
            'currencies'        => self::CURRENCIES,
            'lotteries'         => $this->lotteryModel->forCompany($companyId)->findAll(),
        ]);
    }

    public function store()
    {
        if (!$this->validateData($this->request->getPost(), 'raffleCreate')) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('error', 'Revisa los errores del formulario.');
        }

        $post = $this->request->getPost();
        $companyId = (int) session()->get('company_id');

        $cantidad = (int) $post['ticket_quantity'];
        $numerosPorBoleta = (int) $post['numbers_per_ticket'];
        $longitud = (int) $post['number_length'];
        $totalNumeros = $cantidad * $numerosPorBoleta;
        $maxCombinaciones = pow(10, $longitud);

        if ($totalNumeros > $maxCombinaciones) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'La configuración no es válida. Con ' . $longitud . ' dígitos y ' . $numerosPorBoleta . ' números por boleta, el máximo de boletas es: ' . floor($maxCombinaciones / $numerosPorBoleta));
        }

        $numeroInicio = 0;
        $numeroFin = $totalNumeros - 1;

        $code = $this->generarCodigo($post['name']);
        $bannerImage = $this->procesarImagenBoleta();

        $raffleData = [
            'uuid'                   => $this->generateUuid(),
            'company_id'             => $companyId,
            'name'                   => $post['name'],
            'description'            => $post['description'] ?? null,
            'code'                   => $code,
            'lottery_id'             => $post['lottery_id'],
            'status'                 => $post['status'],
            'ticket_quantity'        => $cantidad,
            'numbers_per_ticket'     => $numerosPorBoleta,
            'number_start'           => str_pad($numeroInicio, $longitud, '0', STR_PAD_LEFT),
            'number_end'             => str_pad($numeroFin, $longitud, '0', STR_PAD_LEFT),
            'number_length'          => $longitud,
            'number_assignment_mode' => $post['tipo_generacion'],
            'number_selection_mode'  => 'BOTH',
            'ticket_price'           => $post['ticket_price'],
            'currency'               => $post['currency'],
            'commission_rate'        => $post['commission_rate'] ?? 0,
            'reservation_minutes'    => $post['reservation_minutes'] ?? 15,
            'terms_conditions'       => $post['terms_conditions'] ?? null,
            'start_at'               => !empty($post['start_at']) ? $post['start_at'] : null,
            'end_at'                 => !empty($post['end_at']) ? $post['end_at'] : null,
        ];

        if ($bannerImage) {
            $this->raffleModel->update($raffleId, ['banner_image' => $bannerImage]);
        }

        $this->crearBoletas($raffleId, $post, $companyId);
        $this->guardarPremios($raffleId, $post['prizes'] ?? []);

        return redirect()->to(base_url('rifas'))
            ->with('success', 'Rifa creada correctamente. Se generaron ' . $cantidad . ' boletas.');
    }

    public function edit(int $id)
    {
        $raffle = $this->findRaffleOrFail($id);
        if (!is_array($raffle)) {
            return $raffle;
        }

        $companyId = (int) session()->get('company_id');

        return view('pages/raffles/edit', [
            'title'             => 'Editar Rifa - Rifas',
            'raffle'            => $raffle,
            'statuses'          => self::STATUSES,
            'assignmentModes'   => self::ASSIGNMENT_MODES,
            'selectionModes'    => self::SELECTION_MODES,
            'currencies'        => self::CURRENCIES,
            'lotteries'         => $this->lotteryModel->forCompany($companyId)->findAll(),
        ]);
    }

    public function update(int $id)
    {
        $raffle = $this->findRaffleOrFail($id);
        if (!is_array($raffle)) {
            return $raffle;
        }

        $data = $this->request->getPost();
        $data['id'] = $id;

        if (!$this->validateData($data, 'raffleUpdate')) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('error', 'Revisa los errores del formulario.');
        }

        $post = $this->request->getPost();

        $updateData = [
            'name'                   => $post['name'],
            'description'            => $post['description'] ?? null,
            'lottery_id'             => $post['lottery_id'],
            'status'                 => $post['status'],
            'ticket_quantity'        => $post['ticket_quantity'],
            'numbers_per_ticket'     => $post['numbers_per_ticket'],
            'number_start'           => $post['number_start'],
            'number_end'             => $post['number_end'],
            'number_length'          => $post['number_length'],
            'number_assignment_mode' => $post['tipo_generacion'],
            'ticket_price'           => $post['ticket_price'],
            'currency'               => $post['currency'],
            'commission_rate'        => $post['commission_rate'] ?? 0,
            'reservation_minutes'    => $post['reservation_minutes'] ?? 15,
            'terms_conditions'       => $post['terms_conditions'] ?? null,
            'start_at'               => !empty($post['start_at']) ? $post['start_at'] : null,
            'end_at'                 => !empty($post['end_at']) ? $post['end_at'] : null,
        ];

        $bannerImage = $this->procesarImagenBoleta();
        if ($bannerImage) {
            $updateData['banner_image'] = $bannerImage;
        }

        $this->raffleModel->update($id, $updateData);

        return redirect()->to(base_url('rifas'))
            ->with('success', 'Rifa actualizada correctamente.');
    }

    public function delete(int $id)
    {
        $raffle = $this->findRaffleOrFail($id);
        if (!is_array($raffle)) {
            return $raffle;
        }

        $this->raffleModel->delete($id);

        return redirect()->to(base_url('rifas'))
            ->with('success', 'Rifa eliminada correctamente.');
    }

    private function procesarImagenBoleta(): ?string
    {
        $file = $this->request->getFile('banner_image');

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

        $nombre = uniqid('raffle_') . '.' . $extension;
        $file->move(ROOTPATH . 'public/uploads/raffles', $nombre);

        return $file->getName() === $nombre ? $nombre : $nombre;
    }

    private function guardarPremios(int $raffleId, array $prizes): void
    {
        if (empty($prizes)) return;

        $prizeModel = new \App\Models\PrizeModel();
        $position = 1;

        foreach ($prizes as $p) {
            if (empty($p['name'])) continue;

            $prizeModel->insert([
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
                'status'          => 'ACTIVE',
                'prize_type'      => $p['type'] ?? 'PRIZE',
                'recurrence_days' => ($p['type'] ?? 'PRIZE') === 'MONEY' && !empty($p['recurrence_days']) ? $p['recurrence_days'] : null,
                'draw_date'       => ($p['type'] ?? 'PRIZE') === 'MONEY' && !empty($p['draw_date']) ? $p['draw_date'] : null,
            ]);
        }
    }

    private function crearBoletas(int $raffleId, array $post, int $companyId): void
    {
        $cantidad = (int) $post['ticket_quantity'];
        $numerosPorBoleta = (int) $post['numbers_per_ticket'];
        $longitud = (int) $post['number_length'];
        $tipoGeneracion = $post['tipo_generacion'];

        $numerosGenerados = $this->generarNumerosUnicos($cantidad, $numerosPorBoleta, $longitud, $tipoGeneracion, $post);

        $data = [];
        for ($i = 1; $i <= $cantidad; $i++) {
            $data[] = [
                'uuid'               => $this->generateUuid(),
                'company_id'         => $companyId,
                'raffle_id'          => $raffleId,
                'code'               => 'B-' . $raffleId . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'status'             => 'AVAILABLE',
                'price'              => $post['ticket_price'],
                'number_quantity'    => $numerosPorBoleta,
                'bundle_quantity'    => 1,
                'created_at'         => date('Y-m-d H:i:s'),
            ];
        }

        if (!empty($data)) {
            $this->ticketModel->insertBatch($data);
        }
    }

    private function generarNumerosUnicos(int $cantidad, int $numerosPorBoleta, int $longitud, string $tipoGeneracion, array $post): array
    {
        $totalNumeros = $cantidad * $numerosPorBoleta;
        $maximo = pow(10, $longitud) - 1;

        if ($totalNumeros > $maximo + 1) {
            throw new \Exception('No hay suficientes combinaciones disponibles para la configuración solicitada.');
        }

        $numeros = [];

        if ($tipoGeneracion === 'SEQUENTIAL') {
            $inicio = (int) $post['number_start'];
            $fin = (int) $post['number_end'];
            $disponibles = $fin - $inicio + 1;

            if ($disponibles < $totalNumeros) {
                throw new \Exception('El rango de números no es suficiente para la cantidad de boletas solicitadas.');
            }

            for ($i = 0; $i < $totalNumeros; $i++) {
                $numeros[] = str_pad($inicio + $i, $longitud, '0', STR_PAD_LEFT);
            }
        } else {
            $intentos = 0;
            $maxIntentos = $totalNumeros * 10;

            while (count($numeros) < $totalNumeros && $intentos < $maxIntentos) {
                $numero = str_pad(random_int(0, $maximo), $longitud, '0', STR_PAD_LEFT);
                if (!in_array($numero, $numeros)) {
                    $numeros[] = $numero;
                }
                $intentos++;
            }

            if (count($numeros) < $totalNumeros) {
                throw new \Exception('No se pudieron generar suficientes números únicos.');
            }
        }

        return $numeros;
    }

    private function generarCodigo(string $nombre): string
    {
        $palabras = explode(' ', trim($nombre));
        $iniciales = '';

        foreach ($palabras as $palabra) {
            if (!empty($palabra)) {
                $iniciales .= strtoupper(substr($palabra, 0, 1));
            }
        }

        $iniciales = substr($iniciales, 0, 3);

        $ultimoCodigo = $this->raffleModel
            ->select('code')
            ->like('code', $iniciales . '-', 'after')
            ->orderBy('id', 'DESC')
            ->first();

        $numero = 1;
        if ($ultimoCodigo) {
            $partes = explode('-', $ultimoCodigo['code']);
            $numero = (int) end($partes) + 1;
        }

        return $iniciales . '-' . str_pad($numero, 3, '0', STR_PAD_LEFT);
    }

    private function findRaffleOrFail(int $id)
    {
        $companyId = (int) session()->get('company_id');

        $raffle = $this->raffleModel
            ->forCompany($companyId)
            ->withDeleted()
            ->find($id);

        if (!$raffle || $raffle['deleted_at'] !== null) {
            return redirect()->to(base_url('rifas'))
                ->with('error', 'La rifa no existe.');
        }

        return $raffle;
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
