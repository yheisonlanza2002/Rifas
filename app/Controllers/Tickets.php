<?php

namespace App\Controllers;

use App\Models\RaffleModel;
use App\Models\TicketModel;

class Tickets extends BaseController
{
    protected TicketModel $ticketModel;
    protected RaffleModel $raffleModel;

    private const STATUSES = [
        'AVAILABLE' => 'Disponible',
        'RESERVED'  => 'Reservado',
        'SOLD'      => 'Vendido',
        'CANCELLED' => 'Cancelado',
    ];

    private const POR_PAGINA = 24;

    public function __construct()
    {
        $this->ticketModel = new TicketModel();
        $this->raffleModel = new RaffleModel();
    }

    public function index(): string
    {
        $companyId = (int) session()->get('company_id');
        $raffleId = (int) ($this->request->getGet('raffle_id') ?? 0);

        $builder = $this->ticketModel
            ->forCompany($companyId)
            ->select('raffles.code AS raffle_code')
            ->orderBy('tickets.id', 'ASC');

        if ($raffleId > 0) {
            $builder->where('tickets.raffle_id', $raffleId);
        }

        $tickets = $builder->findAll();

        $estados = ['AVAILABLE', 'RESERVED', 'SOLD', 'CANCELLED'];
        $grupos = [];
        $totales = ['total' => 0, 'AVAILABLE' => 0, 'RESERVED' => 0, 'SOLD' => 0, 'CANCELLED' => 0];

        foreach ($tickets as $t) {
            $rid = (int) $t['raffle_id'];

            if (!isset($grupos[$rid])) {
                $grupos[$rid] = [
                    'raffle_id'   => $rid,
                    'raffle_name' => $t['raffle_name'] ?? '—',
                    'raffle_code' => $t['raffle_code'] ?? '',
                    'total'       => 0,
                    'AVAILABLE'   => 0,
                    'RESERVED'    => 0,
                    'SOLD'        => 0,
                    'CANCELLED'   => 0,
                    'tickets'     => [],
                    'restantes'   => 0,
                ];
            }

            $estado = in_array($t['status'], $estados, true) ? $t['status'] : 'AVAILABLE';

            $grupos[$rid]['total']++;
            $grupos[$rid][$estado]++;
            $totales['total']++;
            $totales[$estado]++;

            if (count($grupos[$rid]['tickets']) < self::POR_PAGINA) {
                $grupos[$rid]['tickets'][] = $t;
            } else {
                $grupos[$rid]['restantes']++;
            }
        }

        uasort($grupos, static fn(array $a, array $b) => strcmp($a['raffle_name'], $b['raffle_name']));

        return view('pages/tickets/index', [
            'title'          => 'Boletas - Rifas',
            'grupos'         => array_values($grupos),
            'totales'        => $totales,
            'raffles'        => $this->getRaffles(),
            'filterRaffleId' => $raffleId,
        ]);
    }

    public function cargar()
    {
        $companyId = (int) session()->get('company_id');
        $raffleId = (int) $this->request->getGet('raffle_id');
        $offset = max(0, (int) $this->request->getGet('offset'));

        if ($raffleId <= 0) {
            return $this->response->setJSON(['html' => '', 'restantes' => 0]);
        }

        $tickets = $this->ticketModel
            ->forCompany($companyId)
            ->where('tickets.raffle_id', $raffleId)
            ->orderBy('tickets.id', 'ASC')
            ->limit(self::POR_PAGINA, $offset)
            ->findAll();

        $total = $this->db->table('tickets')
            ->where('company_id', $companyId)
            ->where('raffle_id', $raffleId)
            ->countAllResults();

        $html = '';
        foreach ($tickets as $t) {
            $html .= view('pages/tickets/_card', ['t' => $t]);
        }

        return $this->response->setJSON([
            'html'      => $html,
            'restantes' => max(0, $total - $offset - count($tickets)),
        ]);
    }

    public function create(): string
    {
        return view('pages/tickets/create', [
            'title'    => 'Crear Boleta - Rifas',
            'raffles'  => $this->getRaffles(),
            'statuses' => self::STATUSES,
        ]);
    }

    public function store()
    {
        if (!$this->validateData($this->request->getPost(), 'ticketCreate')) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('error', 'Revisa los errores del formulario.');
        }

        $post = $this->request->getPost();

        $this->ticketModel->insert([
            'uuid'              => $this->generateUuid(),
            'company_id'        => session()->get('company_id'),
            'raffle_id'         => $post['raffle_id'],
            'code'              => $post['code'],
            'serial_prefix'     => $post['serial_prefix'] ?? null,
            'status'            => $post['status'],
            'price'             => $post['price'],
            'number_quantity'   => $post['number_quantity'],
            'bundle_quantity'   => $post['bundle_quantity'] ?? 1,
        ]);

        return redirect()->to(base_url('boletas'))
            ->with('success', 'Boleta creada correctamente.');
    }

    public function edit(int $id)
    {
        $ticket = $this->findTicketOrFail($id);
        if (!is_array($ticket)) {
            return $ticket;
        }

        return view('pages/tickets/edit', [
            'title'    => 'Editar Boleta - Rifas',
            'ticket'   => $ticket,
            'raffles'  => $this->getRaffles(),
            'statuses' => self::STATUSES,
        ]);
    }

    public function update(int $id)
    {
        $ticket = $this->findTicketOrFail($id);
        if (!is_array($ticket)) {
            return $ticket;
        }

        $data = $this->request->getPost();
        $data['id'] = $id;

        if (!$this->validateData($data, 'ticketUpdate')) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('error', 'Revisa los errores del formulario.');
        }

        $post = $this->request->getPost();

        $updateData = [
            'raffle_id'       => $post['raffle_id'],
            'code'            => $post['code'],
            'serial_prefix'   => $post['serial_prefix'] ?? null,
            'status'          => $post['status'],
            'price'           => $post['price'],
            'number_quantity' => $post['number_quantity'],
            'bundle_quantity' => $post['bundle_quantity'] ?? 1,
        ];

        if ($post['status'] === 'SOLD' && $ticket['status'] !== 'SOLD') {
            $updateData['sold_at'] = date('Y-m-d H:i:s');
        } elseif ($post['status'] === 'CANCELLED' && $ticket['status'] !== 'CANCELLED') {
            $updateData['cancelled_at'] = date('Y-m-d H:i:s');
        } elseif ($post['status'] === 'RESERVED' && $ticket['status'] !== 'RESERVED') {
            $updateData['reserved_at'] = date('Y-m-d H:i:s');
        }

        $this->ticketModel->update($id, $updateData);

        return redirect()->to(base_url('boletas'))
            ->with('success', 'Boleta actualizada correctamente.');
    }

    public function delete(int $id)
    {
        $ticket = $this->findTicketOrFail($id);
        if (!is_array($ticket)) {
            return $ticket;
        }

        if ($ticket['status'] === 'SOLD') {
            return redirect()->to(base_url('boletas'))
                ->with('error', 'No se puede eliminar una boleta vendida.');
        }

        $this->ticketModel->delete($id);

        return redirect()->to(base_url('boletas'))
            ->with('success', 'Boleta eliminada correctamente.');
    }

    private function getRaffles(): array
    {
        $companyId = (int) session()->get('company_id');

        return $this->raffleModel
            ->forCompany($companyId)
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    private function findTicketOrFail(int $id)
    {
        $companyId = (int) session()->get('company_id');

        $ticket = $this->ticketModel
            ->forCompany($companyId)
            ->find($id);

        if (!$ticket) {
            return redirect()->to(base_url('boletas'))
                ->with('error', 'La boleta no existe.');
        }

        return $ticket;
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
