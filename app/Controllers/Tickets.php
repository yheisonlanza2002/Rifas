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
            ->orderBy('tickets.created_at', 'DESC');

        if ($raffleId > 0) {
            $builder->where('tickets.raffle_id', $raffleId);
        }

        $tickets = $builder->findAll();

        return view('pages/tickets/index', [
            'title'    => 'Boletas - Rifas',
            'tickets'  => $tickets,
            'raffles'  => $this->getRaffles(),
            'statuses' => self::STATUSES,
            'filterRaffleId' => $raffleId,
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
