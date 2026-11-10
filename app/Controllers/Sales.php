<?php

namespace App\Controllers;

use App\Models\ParticipantModel;
use App\Models\RaffleModel;
use App\Models\SaleModel;

class Sales extends BaseController
{
    protected SaleModel $saleModel;
    protected ParticipantModel $participantModel;
    protected RaffleModel $raffleModel;

    private const STATUSES = [
        'PENDING'   => 'Pendiente',
        'PAID'      => 'Pagada',
        'CANCELLED' => 'Cancelada',
    ];

    private const PAYMENT_METHODS = [
        'CASH'     => 'Efectivo',
        'TRANSFER' => 'Transferencia',
    ];

    public function __construct()
    {
        $this->saleModel        = new SaleModel();
        $this->participantModel = new ParticipantModel();
        $this->raffleModel      = new RaffleModel();
    }

    // ------------------------------------------------------------------
    // Historial
    // ------------------------------------------------------------------
    public function index(): string
    {
        $companyId = (int) session()->get('company_id');
        $status    = $this->request->getGet('status') ?? '';
        $q         = trim((string) ($this->request->getGet('q') ?? ''));

        $builder = $this->saleModel
            ->forCompany($companyId)
            ->select('participants.last_name AS participant_last_name', false)
            ->orderBy('sales.id', 'DESC');

        if ($status !== '' && array_key_exists($status, self::STATUSES)) {
            $builder->where('sales.status', $status);
        }

        if ($q !== '') {
            $builder->groupStart()
                ->like('sales.sale_code', $q)
                ->orLike('participants.name', $q)
                ->orLike('participants.last_name', $q)
                ->groupEnd();
        }

        $sales = $builder->findAll();

        $itemsCount = [];
        if ($sales !== []) {
            $ids = array_column($sales, 'id');
            $rows = $this->db->table('sale_items')
                ->select('sale_id, COUNT(*) AS total_items', false)
                ->whereIn('sale_id', $ids)
                ->groupBy('sale_id')
                ->get()
                ->getResultArray();

            foreach ($rows as $row) {
                $itemsCount[(int) $row['sale_id']] = (int) $row['total_items'];
            }
        }

        return view('pages/sales/index', [
            'title'       => 'Ventas - Rifas',
            'sales'       => $sales,
            'itemsCount'  => $itemsCount,
            'statuses'    => self::STATUSES,
            'filterStatus'=> $status,
            'filterQ'     => $q,
        ]);
    }

    // ------------------------------------------------------------------
    // POS — pantalla de venta
    // ------------------------------------------------------------------
    public function create(): string
    {
        $companyId = (int) session()->get('company_id');

        $raffles = $this->raffleModel
            ->forCompany($companyId)
            ->where('raffles.status', 'ACTIVE')
            ->orderBy('raffles.name', 'ASC')
            ->findAll();

        return view('pages/sales/create', [
            'title'        => 'Nueva Venta - Rifas',
            'raffles'      => $raffles,
            'participants' => $this->getParticipants(),
        ]);
    }

    /**
     * GET /ventas/datos-rifa?raffle_id=X — premio, configuración y progreso
     */
    public function datosRifa()
    {
        $companyId = (int) session()->get('company_id');
        $raffleId  = (int) $this->request->getGet('raffle_id');

        $raffle = $this->raffleModel->forCompany($companyId)->find($raffleId);
        if (!$raffle) {
            return $this->response->setJSON(['ok' => false, 'message' => 'La rifa no existe.']);
        }

        $prize = $this->db->table('prizes')
            ->where('raffle_id', $raffleId)
            ->where('company_id', $companyId)
            ->where('status', 'ACTIVE')
            ->orderBy('position', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()
            ->getRowArray();

        $stats = ['AVAILABLE' => 0, 'RESERVED' => 0, 'SOLD' => 0, 'CANCELLED' => 0, 'total' => 0];
        $rows  = $this->db->table('tickets')
            ->select('status, COUNT(*) AS total', false)
            ->where('raffle_id', $raffleId)
            ->where('company_id', $companyId)
            ->groupBy('status')
            ->get()
            ->getResultArray();

        foreach ($rows as $row) {
            $key = $row['status'];
            if (isset($stats[$key])) {
                $stats[$key] = (int) $row['total'];
            }
            $stats['total'] += (int) $row['total'];
        }

        return $this->response->setJSON([
            'ok'    => true,
            'raffle'=> [
                'name'               => $raffle['name'],
                'code'               => $raffle['code'],
                'ticket_price'       => (float) $raffle['ticket_price'],
                'currency'           => $raffle['currency'],
                'numbers_per_ticket' => (int) $raffle['numbers_per_ticket'],
                'number_length'      => (int) $raffle['number_length'],
                'number_start'       => $raffle['number_start'],
                'number_end'         => $raffle['number_end'],
                'ticket_quantity'    => (int) $raffle['ticket_quantity'],
            ],
            'prize' => $prize ? [
                'name'   => $prize['name'],
                'value'  => (float) $prize['value'],
                'type'   => $prize['prize_type'],
                'image'  => !empty($prize['image']) ? base_url($prize['image']) : null,
            ] : null,
            'stats' => $stats,
        ]);
    }

    /**
     * GET /ventas/buscar-numero?raffle_id=X&numero=Y — boleta por número
     */
    public function buscarNumero()
    {
        $companyId = (int) session()->get('company_id');
        $raffleId  = (int) $this->request->getGet('raffle_id');
        $numero    = trim((string) $this->request->getGet('numero'));

        $raffle = $this->raffleModel->forCompany($companyId)->find($raffleId);
        if (!$raffle) {
            return $this->response->setJSON(['ok' => false, 'found' => false, 'message' => 'La rifa no existe.']);
        }

        if ($numero === '' || !preg_match('/^\d+$/', $numero)) {
            return $this->response->setJSON(['ok' => true, 'found' => false, 'message' => 'Ingresa un número válido (solo dígitos).']);
        }

        $numero = str_pad($numero, (int) $raffle['number_length'], '0', STR_PAD_LEFT);

        $row = $this->db->table('ticket_numbers tn')
            ->select('tn.number AS searched_number, t.id AS ticket_id, t.code, t.price, t.status', false)
            ->join('tickets t', 't.id = tn.ticket_id')
            ->where('tn.raffle_id', $raffleId)
            ->where('tn.number', $numero)
            ->get()
            ->getRowArray();

        if (!$row) {
            return $this->response->setJSON([
                'ok'      => true,
                'found'   => false,
                'message' => 'El número ' . $numero . ' no existe en esta rifa.',
            ]);
        }

        $numeros = $this->db->table('ticket_numbers')
            ->select('number', false)
            ->where('ticket_id', (int) $row['ticket_id'])
            ->where('raffle_id', $raffleId)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'ok'      => true,
            'found'   => true,
            'ticket'  => [
                'id'       => (int) $row['ticket_id'],
                'code'     => $row['code'],
                'price'    => (float) $row['price'],
                'status'   => $row['status'],
                'searched' => $numero,
                'numbers'  => array_values(array_column($numeros, 'number')),
            ],
        ]);
    }

    // ------------------------------------------------------------------
    // POS — confirmar venta
    // ------------------------------------------------------------------
    public function store()
    {
        $post = $this->request->getPost();

        if (!$this->validateData($post, 'salePos')) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('error', 'Revisa los errores de la venta.');
        }

        $companyId    = (int) session()->get('company_id');
        $raffleId     = (int) $post['raffle_id'];
        $participantId= (int) $post['participant_id'];
        $method       = $post['payment_method'];
        $reference    = trim((string) ($post['reference'] ?? ''));
        $notes        = trim((string) ($post['notes'] ?? ''));

        $ticketIds = $post['ticket_ids'] ?? [];
        if (!is_array($ticketIds) || $ticketIds === []) {
            return redirect()->back()->withInput()->with('error', 'Agrega al menos una boleta al carrito.');
        }
        $ticketIds = array_values(array_unique(array_map('intval', $ticketIds)));

        if ($method === 'TRANSFER' && $reference === '') {
            return redirect()->back()->withInput()->with('error', 'La transferencia debe incluir la referencia de la operación.');
        }

        $raffle = $this->raffleModel->forCompany($companyId)->find($raffleId);
        if (!$raffle || $raffle['status'] !== 'ACTIVE') {
            return redirect()->back()->withInput()->with('error', 'La rifa no está disponible para ventas.');
        }

        $participant = $this->participantModel->forCompany($companyId)->find($participantId);
        if (!$participant) {
            return redirect()->back()->withInput()->with('error', 'El participante no existe.');
        }

        $tickets = $this->db->table('tickets')
            ->where('raffle_id', $raffleId)
            ->where('company_id', $companyId)
            ->whereIn('id', $ticketIds)
            ->get()
            ->getResultArray();

        if (count($tickets) !== count($ticketIds)) {
            return redirect()->back()->withInput()->with('error', 'Una o más boletas no pertenecen a esta rifa.');
        }

        foreach ($tickets as $t) {
            if ($t['status'] !== 'AVAILABLE') {
                return redirect()->back()->withInput()->with('error', 'La boleta ' . $t['code'] . ' ya no está disponible.');
            }
        }

        $total = 0.0;
        foreach ($tickets as $t) {
            $total += (float) $t['price'];
        }
        $total = round($total, 2);

        $this->db->transBegin();

        try {
            $saleId = $this->saleModel->insert([
                'uuid'              => $this->generateUuid(),
                'company_id'        => $companyId,
                'participant_id'    => $participantId,
                'sale_code'         => $this->generarCodigoVenta(),
                'status'            => 'PENDING',
                'quantity'          => count($tickets),
                'subtotal'          => $total,
                'discount'          => 0,
                'tax_amount'        => 0,
                'commission_amount' => 0,
                'total'             => $total,
                'paid_amount'       => 0,
                'pending_amount'    => $total,
                'currency'          => $raffle['currency'],
                'notes'             => $notes !== '' ? $notes : null,
            ], true);

            if (!$saleId) {
                throw new \Exception('No se pudo registrar la venta.');
            }

            $items = [];
            foreach ($tickets as $t) {
                $items[] = [
                    'uuid'       => $this->generateUuid(),
                    'sale_id'    => (int) $saleId,
                    'ticket_id'  => (int) $t['id'],
                    'quantity'   => 1,
                    'unit_price' => (float) $t['price'],
                    'discount'   => 0,
                    'tax_rate'   => 0,
                    'tax_amount' => 0,
                    'subtotal'   => (float) $t['price'],
                    'created_at' => date('Y-m-d H:i:s'),
                ];
            }
            $this->db->table('sale_items')->insertBatch($items);

            $affected = $this->db->table('tickets')
                ->whereIn('id', $ticketIds)
                ->where('raffle_id', $raffleId)
                ->where('company_id', $companyId)
                ->where('status', 'AVAILABLE')
                ->update([
                    'status'  => 'SOLD',
                    'sold_at' => date('Y-m-d H:i:s'),
                ]);

            if ($affected !== count($ticketIds)) {
                throw new \Exception('Alguien más compró una de estas boletas al mismo tiempo. Intenta de nuevo.');
            }

            if ($method !== 'CREDIT') {
                $this->db->table('payments')->insert([
                    'uuid'                  => $this->generateUuid(),
                    'company_id'            => $companyId,
                    'sale_id'               => (int) $saleId,
                    'amount'                => $total,
                    'fee_amount'            => 0,
                    'net_amount'            => $total,
                    'payment_method'        => $method,
                    'status'                => 'COMPLETED',
                    'transaction_reference' => $reference !== '' ? $reference : null,
                    'paid_at'               => date('Y-m-d H:i:s'),
                    'created_at'            => date('Y-m-d H:i:s'),
                ]);
            }

            $this->saleModel->syncFromPayments((int) $saleId);

            $this->db->transCommit();

            $saleCode = $this->saleModel->find($saleId)['sale_code'] ?? '';
            $mensaje  = $method === 'CREDIT'
                ? 'Venta ' . $saleCode . ' registrada. Queda pendiente de pago.'
                : 'Venta ' . $saleCode . ' completada. Boletas vendidas correctamente.';

            return redirect()->to(base_url('ventas'))
                ->with('success', $mensaje);
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage() !== '' ? $e->getMessage() : 'No se pudo completar la venta.');
        }
    }

    // ------------------------------------------------------------------
    // Registrar pago de una venta pendiente
    // ------------------------------------------------------------------
    public function formRegistrarPago(int $id)
    {
        $sale = $this->findSaleOrFail($id);
        if (!is_array($sale)) {
            return $sale;
        }

        if ($sale['status'] === 'CANCELLED') {
            return redirect()->to(base_url('ventas'))->with('error', 'No se puede registrar pago a una venta cancelada.');
        }

        if ((float) $sale['pending_amount'] <= 0) {
            return redirect()->to(base_url('ventas'))->with('error', 'La venta ya está saldada.');
        }

        $participant = $this->participantModel
            ->forCompany((int) session()->get('company_id'))
            ->find((int) $sale['participant_id']);

        return view('pages/sales/registrar_pago', [
            'title'       => 'Registrar Pago - Rifas',
            'sale'        => $sale,
            'participant' => $participant,
            'methods'     => self::PAYMENT_METHODS,
        ]);
    }

    public function registrarPago(int $id)
    {
        $sale = $this->findSaleOrFail($id);
        if (!is_array($sale)) {
            return $sale;
        }

        $post = $this->request->getPost();
        $post['id'] = $id;

        if (!$this->validateData($post, 'registrarPago')) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('error', 'Revisa los errores del pago.');
        }

        if ($sale['status'] === 'CANCELLED') {
            return redirect()->to(base_url('ventas'))->with('error', 'No se puede registrar pago a una venta cancelada.');
        }

        $amount   = round((float) $post['amount'], 2);
        $pending  = round((float) $sale['pending_amount'], 2);
        $method   = $post['payment_method'];
        $reference = trim((string) ($post['reference'] ?? ''));

        if ($method === 'TRANSFER' && $reference === '') {
            return redirect()->back()->withInput()->with('error', 'La transferencia debe incluir la referencia de la operación.');
        }

        if ($amount > $pending + 0.005) {
            return redirect()->back()->withInput()->with('error', 'El monto no puede superar el saldo pendiente ($ ' . number_format($pending, 2) . ').');
        }

        $this->db->table('payments')->insert([
            'uuid'                  => $this->generateUuid(),
            'company_id'            => (int) session()->get('company_id'),
            'sale_id'               => (int) $id,
            'amount'                => $amount,
            'fee_amount'            => 0,
            'net_amount'            => $amount,
            'payment_method'        => $method,
            'status'                => 'COMPLETED',
            'transaction_reference' => $reference !== '' ? $reference : null,
            'paid_at'               => date('Y-m-d H:i:s'),
            'created_at'            => date('Y-m-d H:i:s'),
        ]);

        $this->saleModel->syncFromPayments((int) $id);

        $sale = $this->saleModel->find($id);
        $mensaje = ($sale['status'] ?? '') === 'PAID'
            ? 'Pago registrado. La venta ' . $sale['sale_code'] . ' está saldada.'
            : 'Pago parcial registrado. Saldo pendiente: $ ' . number_format((float) $sale['pending_amount'], 2) . '.';

        return redirect()->to(base_url('ventas'))->with('success', $mensaje);
    }

    // ------------------------------------------------------------------
    // Eliminar
    // ------------------------------------------------------------------
    public function delete(int $id)
    {
        $sale = $this->findSaleOrFail($id);
        if (!is_array($sale)) {
            return $sale;
        }

        if ($sale['status'] === 'PAID') {
            return redirect()->to(base_url('ventas'))
                ->with('error', 'No se puede eliminar una venta pagada.');
        }

        $pagos = $this->db->table('payments')->where('sale_id', $id)->countAllResults();
        if ($pagos > 0) {
            return redirect()->to(base_url('ventas'))
                ->with('error', 'La venta tiene pagos registrados y no se puede eliminar.');
        }

        $this->db->table('sale_items')->where('sale_id', $id)->delete();
        $this->saleModel->delete($id);

        return redirect()->to(base_url('ventas'))
            ->with('success', 'Venta eliminada correctamente.');
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------
    private function getParticipants(): array
    {
        $companyId = (int) session()->get('company_id');

        return $this->participantModel
            ->forCompany($companyId)
            ->where('status', 'ACTIVE')
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    private function findSaleOrFail(int $id)
    {
        $companyId = (int) session()->get('company_id');

        $sale = $this->saleModel
            ->forCompany($companyId)
            ->find($id);

        if (!$sale) {
            return redirect()->to(base_url('ventas'))
                ->with('error', 'La venta no existe.');
        }

        return $sale;
    }

    private function generarCodigoVenta(): string
    {
        return 'V-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
    }

    private function generateUuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0x8000, 0xbfff), mt_rand(0x8000, 0xbfff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
