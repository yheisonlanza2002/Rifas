<?php

namespace App\Controllers;

use App\Models\DrawModel;
use App\Models\RaffleModel;
use App\Models\PrizeModel;
use App\Models\TicketModel;
use App\Models\WinnerModel;
use App\Models\TicketEventModel;

class Draws extends BaseController
{
    protected DrawModel $drawModel;
    protected RaffleModel $raffleModel;
    protected PrizeModel $prizeModel;
    protected TicketModel $ticketModel;
    protected WinnerModel $winnerModel;
    protected TicketEventModel $ticketEventModel;

    private const STATUSES = [
        'SCHEDULED' => 'Programado',
        'EXECUTING' => 'Ejecutando',
        'COMPLETED' => 'Completado',
        'CANCELLED' => 'Cancelado',
    ];

    private const METHODS = [
        'MANUAL'        => 'Manual',
        'RANDOM'        => 'Aleatorio',
        'EXTERNAL_API'  => 'API Externa',
        'BLOCKCHAIN'    => 'Blockchain',
    ];

    private const WINNER_STATUSES = [
        'PENDING'   => 'Pendiente',
        'ANNOUNCED' => 'Anunciado',
        'CLAIMED'   => 'Reclamado',
        'EXPIRED'   => 'Expirado',
    ];

    public function __construct()
    {
        $this->drawModel = new DrawModel();
        $this->raffleModel = new RaffleModel();
        $this->prizeModel = new PrizeModel();
        $this->ticketModel = new TicketModel();
        $this->winnerModel = new WinnerModel();
        $this->ticketEventModel = new TicketEventModel();
    }

    public function index(): string
    {
        $companyId = (int) session()->get('company_id');

        $draws = $this->drawModel
            ->forCompany($companyId)
            ->orderBy('draws.scheduled_at', 'DESC')
            ->findAll();

        return view('pages/draws/index', [
            'title'    => 'Sorteos - Rifas',
            'draws'    => $draws,
            'statuses' => self::STATUSES,
            'methods'  => self::METHODS,
        ]);
    }

    public function create(): string
    {
        return view('pages/draws/create', [
            'title'   => 'Crear Sorteo - Rifas',
            'raffles' => $this->getRaffles(),
            'methods' => self::METHODS,
        ]);
    }

    public function store()
    {
        if (!$this->validateData($this->request->getPost(), 'drawCreate')) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('error', 'Revisa los errores del formulario.');
        }

        $post = $this->request->getPost();
        $companyId = (int) session()->get('company_id');

        $this->drawModel->insert([
            'uuid'               => $this->generateUuid(),
            'company_id'         => $companyId,
            'raffle_id'          => $post['raffle_id'],
            'name'               => $post['name'],
            'scheduled_at'        => $post['scheduled_at'],
            'status'             => 'SCHEDULED',
            'method'             => $post['method'],
            'seed'               => $post['seed'] ?? null,
            'external_reference' => $post['external_reference'] ?? null,
            'created_by'         => session()->get('user_id'),
        ]);

        return redirect()->to(base_url('sorteos'))
            ->with('success', 'Sorteo programado correctamente.');
    }

    public function edit(int $id)
    {
        $draw = $this->findDrawOrFail($id);
        if (!is_array($draw)) {
            return $draw;
        }

        return view('pages/draws/edit', [
            'title'   => 'Editar Sorteo - Rifas',
            'draw'    => $draw,
            'raffles' => $this->getRaffles(),
            'methods' => self::METHODS,
        ]);
    }

    public function update(int $id)
    {
        $draw = $this->findDrawOrFail($id);
        if (!is_array($draw)) {
            return $draw;
        }

        if ($draw['status'] !== 'SCHEDULED') {
            return redirect()->to(base_url('sorteos'))
                ->with('error', 'Solo se pueden editar sorteos programados.');
        }

        $data = $this->request->getPost();
        $data['id'] = $id;

        if (!$this->validateData($data, 'drawUpdate')) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('error', 'Revisa los errores del formulario.');
        }

        $post = $this->request->getPost();

        $this->drawModel->update($id, [
            'raffle_id'          => $post['raffle_id'],
            'name'               => $post['name'],
            'scheduled_at'        => $post['scheduled_at'],
            'method'             => $post['method'],
            'seed'               => $post['seed'] ?? null,
            'external_reference' => $post['external_reference'] ?? null,
        ]);

        return redirect()->to(base_url('sorteos'))
            ->with('success', 'Sorteo actualizado correctamente.');
    }

    public function delete(int $id)
    {
        $draw = $this->findDrawOrFail($id);
        if (!is_array($draw)) {
            return $draw;
        }

        if ($draw['status'] === 'COMPLETED') {
            return redirect()->to(base_url('sorteos'))
                ->with('error', 'No se puede eliminar un sorteo completado.');
        }

        $this->drawModel->delete($id);

        return redirect()->to(base_url('sorteos'))
            ->with('success', 'Sorteo eliminado correctamente.');
    }

    public function execute(int $id)
    {
        $draw = $this->findDrawOrFail($id);
        if (!is_array($draw)) {
            return $draw;
        }

        if ($draw['status'] !== 'SCHEDULED') {
            return redirect()->to(base_url('sorteos'))
                ->with('error', 'El sorteo ya fue ejecutado o cancelado.');
        }

        $companyId = (int) session()->get('company_id');
        $prizes = $this->prizeModel
            ->forCompany($companyId)
            ->where('raffle_id', $draw['raffle_id'])
            ->where('status', 'ACTIVE')
            ->orderBy('position', 'ASC')
            ->findAll();

        if (empty($prizes)) {
            return redirect()->to(base_url('sorteos'))
                ->with('error', 'No hay premios activos para esta rifa.');
        }

        $soldTickets = $this->getSoldTickets($draw['raffle_id']);

        if (empty($soldTickets)) {
            return redirect()->to(base_url('sorteos'))
                ->with('error', 'No hay boletas vendidas para esta rifa.');
        }

        $seed = $draw['seed'] ?? $this->generateSeed();
        $winners = $this->selectWinners($prizes, $soldTickets, $seed);

        $this->drawModel->update($id, [
            'status'      => 'EXECUTING',
            'executed_at' => date('Y-m-d H:i:s'),
        ]);

        $resultHash = $this->calculateResultHash($winners, $seed);

        foreach ($winners as $winner) {
            $this->winnerModel->insert([
                'uuid'             => $this->generateUuid(),
                'draw_id'          => $id,
                'raffle_id'        => $draw['raffle_id'],
                'prize_id'         => $winner['prize_id'],
                'ticket_id'        => $winner['ticket_id'],
                'ticket_number_id' => $winner['ticket_number_id'],
                'participant_id'   => $winner['participant_id'],
                'status'           => 'PENDING',
            ]);

            $this->ticketEventModel->insert([
                'uuid'             => $this->generateUuid(),
                'raffle_id'        => $draw['raffle_id'],
                'ticket_id'        => $winner['ticket_id'],
                'ticket_number_id' => $winner['ticket_number_id'],
                'event_type'       => 'WINNER_SELECTED',
                'new_status'       => 'SOLD',
                'reference_type'   => 'draw',
                'reference_id'     => $id,
                'description'      => 'Boleta seleccionada como ganadora',
                'metadata'         => json_encode(['prize_id' => $winner['prize_id'], 'seed' => $seed]),
                'created_by'       => session()->get('user_id'),
            ]);
        }

        $this->drawModel->update($id, [
            'status'      => 'COMPLETED',
            'result_hash' => $resultHash,
        ]);

        return redirect()->to(base_url('sorteos'))
            ->with('success', 'Sorteo ejecutado correctamente. ' . count($winners) . ' ganador(es) seleccionado(s).');
    }

    public function results(int $id): string
    {
        $draw = $this->findDrawOrFail($id);
        if (!is_array($draw)) {
            return redirect()->to(base_url('sorteos'))
                ->with('error', 'El sorteo no existe.');
        }

        $winners = $this->winnerModel->findByDraw($id);

        $prizes = $this->prizeModel
            ->whereIn('id', array_column($winners, 'prize_id'))
            ->findAll();
        $prizeMap = array_column($prizes, null, 'id');

        $participants = [];
        if (!empty($winners)) {
            $participantIds = array_unique(array_column($winners, 'participant_id'));
            $participants = $this->db->table('participants')
                ->whereIn('id', $participantIds)
                ->get()
                ->getResultArray();
        }
        $participantMap = array_column($participants, null, 'id');

        return view('pages/draws/results', [
            'title'           => 'Resultados del Sorteo - Rifas',
            'draw'            => $draw,
            'winners'         => $winners,
            'prizeMap'        => $prizeMap,
            'participantMap'  => $participantMap,
            'winnerStatuses'  => self::WINNER_STATUSES,
            'methods'         => self::METHODS,
        ]);
    }

    public function cancel(int $id)
    {
        $draw = $this->findDrawOrFail($id);
        if (!is_array($draw)) {
            return $draw;
        }

        if ($draw['status'] !== 'SCHEDULED') {
            return redirect()->to(base_url('sorteos'))
                ->with('error', 'Solo se pueden cancelar sorteos programados.');
        }

        $this->drawModel->update($id, ['status' => 'CANCELLED']);

        return redirect()->to(base_url('sorteos'))
            ->with('success', 'Sorteo cancelado correctamente.');
    }

    private function getSoldTickets(int $raffleId): array
    {
        return $this->db->table('tickets t')
            ->select('t.id, t.code, t.raffle_id, s.participant_id')
            ->join('sale_items si', 'si.ticket_id = t.id')
            ->join('sales s', 's.id = si.sale_id')
            ->where('t.raffle_id', $raffleId)
            ->where('t.status', 'SOLD')
            ->where('s.status', 'PAID')
            ->get()
            ->getResultArray();
    }

    private function selectWinners(array $prizes, array $tickets, string $seed): array
    {
        mt_srand(crc32($seed));
        shuffle($tickets);

        $winners = [];
        $usedTicketIds = [];

        foreach ($prizes as $prize) {
            $quantity = (int) $prize['quantity'];

            for ($i = 0; $i < $quantity; $i++) {
                $ticket = null;
                foreach ($tickets as $t) {
                    if (!in_array($t['id'], $usedTicketIds)) {
                        $ticket = $t;
                        break;
                    }
                }

                if (!$ticket) {
                    break;
                }

                $usedTicketIds[] = $ticket['id'];

                $ticketNumber = $this->db->table('ticket_numbers')
                    ->where('ticket_id', $ticket['id'])
                    ->get()
                    ->getRowArray();

                $winners[] = [
                    'prize_id'         => $prize['id'],
                    'ticket_id'        => $ticket['id'],
                    'ticket_number_id' => $ticketNumber['id'] ?? null,
                    'participant_id'   => $ticket['participant_id'],
                ];
            }
        }

        mt_srand();

        return $winners;
    }

    private function calculateResultHash(array $winners, string $seed): string
    {
        $data = [
            'seed'    => $seed,
            'winners' => array_map(fn($w) => [
                'ticket_id'      => $w['ticket_id'],
                'ticket_number'  => $w['ticket_number_id'],
                'participant_id' => $w['participant_id'],
                'prize_id'       => $w['prize_id'],
            ], $winners),
        ];

        return hash('sha256', json_encode($data));
    }

    private function generateSeed(): string
    {
        return bin2hex(random_bytes(16));
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

    private function findDrawOrFail(int $id)
    {
        $companyId = (int) session()->get('company_id');

        $draw = $this->drawModel
            ->forCompany($companyId)
            ->find($id);

        if (!$draw) {
            return redirect()->to(base_url('sorteos'))
                ->with('error', 'El sorteo no existe.');
        }

        return $draw;
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
