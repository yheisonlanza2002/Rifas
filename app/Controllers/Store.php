<?php

namespace App\Controllers;

use App\Libraries\WompiLibrary;
use App\Models\ParticipantModel;
use App\Models\PaymentGatewayModel;
use App\Models\PrizeModel;
use App\Models\RaffleModel;
use App\Models\SaleModel;
use App\Models\TicketModel;
use CodeIgniter\Controller;

class Store extends Controller
{
    protected $db;
    protected RaffleModel $raffleModel;
    protected TicketModel $ticketModel;
    protected PrizeModel $prizeModel;
    protected SaleModel $saleModel;
    protected ParticipantModel $participantModel;
    protected PaymentGatewayModel $gatewayModel;

    public function __construct()
    {
        $this->db               = \Config\Database::connect();
        $this->raffleModel      = new RaffleModel();
        $this->ticketModel      = new TicketModel();
        $this->prizeModel       = new PrizeModel();
        $this->saleModel        = new SaleModel();
        $this->participantModel = new ParticipantModel();
        $this->gatewayModel     = new PaymentGatewayModel();
    }

    /**
     * Catálogo público de rifas activas.
     * GET /tienda
     */
    public function index()
    {
        $raffles = $this->raffleModel
            ->where('status', 'ACTIVE')
            ->where('deleted_at', null)
            ->orderBy('id', 'DESC')
            ->findAll();

        foreach ($raffles as &$raffle) {
            // Premio principal
            $prize = $this->db->table('prizes')
                ->where('raffle_id', $raffle['id'])
                ->where('status', 'ACTIVE')
                ->orderBy('position', 'ASC')
                ->orderBy('id', 'ASC')
                ->get()
                ->getRowArray();

            $raffle['main_prize'] = $prize;

            // Lotería vinculada
            if (!empty($raffle['lottery_id'])) {
                $lottery = $this->db->table('lotteries')->where('id', $raffle['lottery_id'])->get()->getRowArray();
                $raffle['lottery'] = $lottery;
            } else {
                $raffle['lottery'] = null;
            }

            // Estadísticas de boletas
            $stats = $this->db->table('tickets')
                ->select("
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'SOLD' THEN 1 ELSE 0 END) as sold,
                    SUM(CASE WHEN status = 'AVAILABLE' THEN 1 ELSE 0 END) as available
                ")
                ->where('raffle_id', $raffle['id'])
                ->get()
                ->getRowArray();

            $raffle['stats'] = [
                'total'     => (int) ($stats['total'] ?? 0),
                'sold'      => (int) ($stats['sold'] ?? 0),
                'available' => (int) ($stats['available'] ?? 0),
                'percent'   => !empty($stats['total']) ? round(($stats['sold'] / $stats['total']) * 100) : 0,
            ];
        }
        unset($raffle);

        return view('pages/store/index', [
            'title'   => 'Rifas Disponibles - Compra tus números en línea',
            'raffles' => $raffles,
        ]);
    }

    /**
     * Detalle de una rifa y selector interactivo de boletas.
     * GET /tienda/rifa/(:segment)
     */
    public function show(string $codeOrId)
    {
        $builder = $this->raffleModel->where('status', 'ACTIVE')->where('deleted_at', null);

        if (is_numeric($codeOrId)) {
            $raffle = $builder->where('id', (int) $codeOrId)->first();
        } else {
            $raffle = $builder->where('code', $codeOrId)->first();
        }

        if (!$raffle) {
            return redirect()->to(base_url('tienda'))
                ->with('error', 'La rifa solicitada no existe o ya no está disponible.');
        }

        // Lotería vinculada
        $lottery = null;
        if (!empty($raffle['lottery_id'])) {
            $lottery = $this->db->table('lotteries')->where('id', $raffle['lottery_id'])->get()->getRowArray();
        }

        // Premios de la rifa
        $prizes = $this->prizeModel
            ->where('raffle_id', $raffle['id'])
            ->where('status', 'ACTIVE')
            ->orderBy('position', 'ASC')
            ->findAll();

        // Estadísticas de boletas
        $stats = $this->db->table('tickets')
            ->select("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'SOLD' THEN 1 ELSE 0 END) as sold,
                SUM(CASE WHEN status = 'AVAILABLE' THEN 1 ELSE 0 END) as available
            ")
            ->where('raffle_id', $raffle['id'])
            ->get()
            ->getRowArray();

        $statsData = [
            'total'     => (int) ($stats['total'] ?? 0),
            'sold'      => (int) ($stats['sold'] ?? 0),
            'available' => (int) ($stats['available'] ?? 0),
            'percent'   => !empty($stats['total']) ? round(($stats['sold'] / $stats['total']) * 100) : 0,
        ];

        // Muestra inicial de boletas disponibles (primeras 60 para elegir rápido)
        $initialTickets = $this->db->table('tickets t')
            ->select('t.id, t.code, t.price, t.status')
            ->where('t.raffle_id', $raffle['id'])
            ->where('t.status', 'AVAILABLE')
            ->orderBy('t.id', 'ASC')
            ->limit(60)
            ->get()
            ->getResultArray();

        // Cargar números de esas boletas
        if (!empty($initialTickets)) {
            $tIds = array_column($initialTickets, 'id');
            $numbersRows = $this->db->table('ticket_numbers')
                ->select('ticket_id, number')
                ->whereIn('ticket_id', $tIds)
                ->orderBy('id', 'ASC')
                ->get()
                ->getResultArray();

            $numbersByTicket = [];
            foreach ($numbersRows as $nr) {
                $numbersByTicket[$nr['ticket_id']][] = $nr['number'];
            }

            foreach ($initialTickets as &$t) {
                $t['numbers'] = $numbersByTicket[$t['id']] ?? [];
            }
            unset($t);
        }

        // Pasarelas activas para la empresa
        $gateways = $this->gatewayModel
            ->forCompany((int) $raffle['company_id'])
            ->where('status', 'ACTIVE')
            ->orderBy('sort_order', 'ASC')
            ->findAll();

        return view('pages/store/show', [
            'title'          => esc($raffle['name']) . ' - Participa en línea',
            'raffle'         => $raffle,
            'lottery'        => $lottery,
            'prizes'         => $prizes,
            'stats'          => $statsData,
            'initialTickets' => $initialTickets,
            'gateways'       => $gateways,
        ]);
    }

    /**
     * Búsqueda AJAX de número en una rifa o selección aleatoria.
     * GET /tienda/buscar-numero?raffle_id=X&q=Y
     */
    public function searchNumber()
    {
        $raffleId = (int) $this->request->getGet('raffle_id');
        $query    = trim((string) $this->request->getGet('q'));
        $random   = (int) $this->request->getGet('random');

        if ($raffleId <= 0) {
            return $this->response->setJSON(['ok' => false, 'message' => 'Rifa no especificada.']);
        }

        $raffle = $this->raffleModel->where('status', 'ACTIVE')->find($raffleId);
        if (!$raffle) {
            return $this->response->setJSON(['ok' => false, 'message' => 'Rifa no válida.']);
        }

        // Caso selección aleatoria
        if ($random > 0) {
            $count = min(max($random, 1), 20); // máximo 20 aleatorios por solicitud
            $randomTickets = $this->db->table('tickets')
                ->select('id, code, price, status')
                ->where('raffle_id', $raffleId)
                ->where('status', 'AVAILABLE')
                ->orderBy('RAND()')
                ->limit($count)
                ->get()
                ->getResultArray();

            if (empty($randomTickets)) {
                return $this->response->setJSON(['ok' => false, 'message' => 'No hay boletas disponibles en este momento.']);
            }

            $tIds = array_column($randomTickets, 'id');
            $numbersRows = $this->db->table('ticket_numbers')
                ->select('ticket_id, number')
                ->whereIn('ticket_id', $tIds)
                ->get()
                ->getResultArray();

            $numbersMap = [];
            foreach ($numbersRows as $row) {
                $numbersMap[$row['ticket_id']][] = $row['number'];
            }

            foreach ($randomTickets as &$rt) {
                $rt['numbers'] = $numbersMap[$rt['id']] ?? [];
            }
            unset($rt);

            return $this->response->setJSON(['ok' => true, 'tickets' => $randomTickets]);
        }

        // Caso búsqueda por número específico
        if ($query === '') {
            return $this->response->setJSON(['ok' => false, 'message' => 'Ingresa un número para buscar.']);
        }

        $padded = str_pad($query, (int) ($raffle['number_length'] ?? 0), '0', STR_PAD_LEFT);

        $row = $this->db->table('ticket_numbers tn')
            ->select('t.id as ticket_id, t.code, t.price, t.status, tn.number')
            ->join('tickets t', 't.id = tn.ticket_id')
            ->where('tn.raffle_id', $raffleId)
            ->groupStart()
                ->where('tn.number', $query)
                ->orWhere('tn.number', $padded)
            ->groupEnd()
            ->get()
            ->getRowArray();

        if (!$row) {
            return $this->response->setJSON([
                'ok'      => true,
                'found'   => false,
                'message' => 'El número ' . $query . ' no existe en esta rifa.',
            ]);
        }

        // Obtener todos los números de esa boleta
        $numbers = $this->db->table('ticket_numbers')
            ->select('number')
            ->where('ticket_id', (int) $row['ticket_id'])
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'ok'     => true,
            'found'  => true,
            'ticket' => [
                'id'      => (int) $row['ticket_id'],
                'code'    => $row['code'],
                'price'   => (float) $row['price'],
                'status'  => $row['status'],
                'numbers' => array_column($numbers, 'number'),
            ],
        ]);
    }

    /**
     * Procesa la compra / reserva desde la tienda pública.
     * POST /tienda/checkout
     */
    public function checkout()
    {
        $post = $this->request->getPost();

        $raffleId       = (int) ($post['raffle_id'] ?? 0);
        $name           = trim((string) ($post['name'] ?? ''));
        $lastName       = trim((string) ($post['last_name'] ?? ''));
        $documentType   = trim((string) ($post['document_type'] ?? 'CC'));
        $documentNumber = trim((string) ($post['document_number'] ?? ''));
        $phone          = trim((string) ($post['phone'] ?? ''));
        $email          = trim((string) ($post['email'] ?? ''));
        $paymentDriver  = trim((string) ($post['payment_driver'] ?? 'wompi'));
        $ticketIds      = $post['ticket_ids'] ?? [];

        // Validaciones básicas
        if ($raffleId <= 0) {
            return redirect()->back()->withInput()->with('error', 'Rifa no especificada.');
        }

        if ($name === '' || $phone === '') {
            return redirect()->back()->withInput()->with('error', 'Por favor ingresa tu nombre completo y teléfono móvil.');
        }

        if (!is_array($ticketIds) || empty($ticketIds)) {
            return redirect()->back()->withInput()->with('error', 'Debes seleccionar al menos una boleta.');
        }

        $ticketIds = array_values(array_unique(array_map('intval', $ticketIds)));

        $raffle = $this->raffleModel->where('status', 'ACTIVE')->find($raffleId);
        if (!$raffle) {
            return redirect()->back()->withInput()->with('error', 'La rifa no está disponible.');
        }

        $companyId = (int) $raffle['company_id'];

        // Verificar disponibilidad de los tickets
        $tickets = $this->db->table('tickets')
            ->where('raffle_id', $raffleId)
            ->where('company_id', $companyId)
            ->whereIn('id', $ticketIds)
            ->get()
            ->getResultArray();

        if (count($tickets) !== count($ticketIds)) {
            return redirect()->back()->withInput()->with('error', 'Una o más boletas seleccionadas no son válidas.');
        }

        foreach ($tickets as $t) {
            if ($t['status'] !== 'AVAILABLE') {
                return redirect()->back()->withInput()->with('error', 'La boleta ' . $t['code'] . ' ya no está disponible. Por favor selecciona otra.');
            }
        }

        // 1. Buscar o registrar participante
        $participant = null;
        if (!empty($documentNumber)) {
            $participant = $this->participantModel
                ->forCompany($companyId)
                ->where('document_number', $documentNumber)
                ->first();
        }

        if (!$participant && !empty($phone)) {
            $participant = $this->participantModel
                ->forCompany($companyId)
                ->where('phone', $phone)
                ->first();
        }

        if (!$participant) {
            $participantId = $this->participantModel->insert([
                'uuid'            => $this->generateUuid(),
                'company_id'      => $companyId,
                'name'            => $name,
                'last_name'       => $lastName !== '' ? $lastName : null,
                'document_type'   => $documentType,
                'document_number' => $documentNumber !== '' ? $documentNumber : null,
                'phone'           => $phone,
                'email'           => $email !== '' ? $email : null,
                'status'          => 'ACTIVE',
            ], true);
        } else {
            $participantId = (int) $participant['id'];
            // Actualizar datos si viene email o nombre
            $this->participantModel->update($participantId, [
                'name'      => $name,
                'last_name' => $lastName !== '' ? $lastName : $participant['last_name'],
                'email'     => $email !== '' ? $email : $participant['email'],
                'phone'     => $phone,
            ]);
        }

        // 2. Calcular total
        $total = 0.0;
        foreach ($tickets as $t) {
            $total += (float) $t['price'];
        }
        $total = round($total, 2);

        // 3. Crear Venta en estado PENDING y reservar boletas
        $this->db->transBegin();
        try {
            $saleCode = $this->generarCodigoVenta();
            $saleUuid = $this->generateUuid();

            $saleId = $this->saleModel->insert([
                'uuid'              => $saleUuid,
                'company_id'        => $companyId,
                'participant_id'    => (int) $participantId,
                'sale_code'         => $saleCode,
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
                'notes'             => 'Compra web en línea - ' . strtoupper($paymentDriver),
            ], true);

            // Insertar items de venta
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

            // Marcar boletas como RESERVED temporalmente
            $this->db->table('tickets')
                ->whereIn('id', $ticketIds)
                ->where('status', 'AVAILABLE')
                ->update([
                    'status' => 'RESERVED',
                ]);

            $this->db->transCommit();

            return redirect()->to(base_url('tienda/pago/' . $saleUuid));
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Error al procesar la reserva: ' . $e->getMessage());
        }
    }

    /**
     * Pantalla de pago: Botón Wompi o instrucciones de Pago Manual.
     * GET /tienda/pago/(:segment)
     */
    public function payment(string $saleUuid)
    {
        $sale = $this->db->table('sales')->where('uuid', $saleUuid)->get()->getRowArray();
        if (!$sale) {
            return redirect()->to(base_url('tienda'))->with('error', 'La orden de compra no existe.');
        }

        if ($sale['status'] === 'PAID') {
            return redirect()->to(base_url('tienda/confirmacion/' . $saleUuid));
        }

        $participant = $this->participantModel->find((int) $sale['participant_id']);

        // Obtener ítems y boletas compradas
        $items = $this->db->table('sale_items si')
            ->select('t.id as ticket_id, t.code, t.price')
            ->join('tickets t', 't.id = si.ticket_id')
            ->where('si.sale_id', $sale['id'])
            ->get()
            ->getResultArray();

        $tIds = array_column($items, 'ticket_id');
        $numbersRows = !empty($tIds) ? $this->db->table('ticket_numbers')
            ->select('ticket_id, number')
            ->whereIn('ticket_id', $tIds)
            ->get()
            ->getResultArray() : [];

        $numbersMap = [];
        foreach ($numbersRows as $row) {
            $numbersMap[$row['ticket_id']][] = $row['number'];
        }

        foreach ($items as &$it) {
            $it['numbers'] = $numbersMap[$it['ticket_id']] ?? [];
        }
        unset($it);

        // Obtener información de la rifa
        $raffle = $this->db->table('tickets t')
            ->select('r.*')
            ->join('raffles r', 'r.id = t.raffle_id')
            ->where('t.id', $items[0]['ticket_id'] ?? 0)
            ->get()
            ->getRowArray();

        // Buscar pasarelas activas
        $companyId = (int) $sale['company_id'];
        $gateways  = $this->gatewayModel
            ->forCompany($companyId)
            ->where('status', 'ACTIVE')
            ->orderBy('sort_order', 'ASC')
            ->findAll();

        $wompiGateway  = null;
        $manualGateway = null;
        $wompiConfig   = [];
        $manualConfig  = [];
        $wompiSignature = '';

        foreach ($gateways as $gw) {
            if ($gw['driver'] === 'wompi' && !$wompiGateway) {
                $wompiGateway = $gw;
                $wompiConfig  = json_decode($gw['config'] ?? '{}', true) ?: [];
            }
            if ($gw['driver'] === 'manual' && !$manualGateway) {
                $manualGateway = $gw;
                $manualConfig  = json_decode($gw['config'] ?? '{}', true) ?: [];
            }
        }

        // Generar firma de integridad si Wompi está disponible y tiene events_key o integrity secret
        if ($wompiGateway && !empty($wompiConfig['events_key'])) {
            $amountInCents = (int) round(((float) $sale['total']) * 100);
            $wompiSignature = WompiLibrary::generateIntegritySignature(
                $sale['sale_code'],
                $amountInCents,
                $sale['currency'] ?? 'COP',
                $wompiConfig['events_key']
            );
        }

        return view('pages/store/payment', [
            'title'          => 'Pagar Orden ' . $sale['sale_code'] . ' - Rifas',
            'sale'           => $sale,
            'raffle'         => $raffle,
            'participant'    => $participant,
            'items'          => $items,
            'wompiGateway'   => $wompiGateway,
            'wompiConfig'    => $wompiConfig,
            'wompiSignature' => $wompiSignature,
            'manualGateway'  => $manualGateway,
            'manualConfig'   => $manualConfig,
        ]);
    }

    /**
     * Página de confirmación y comprobante para el comprador.
     * GET /tienda/confirmacion/(:segment)
     */
    public function confirmation(string $saleUuid)
    {
        $sale = $this->db->table('sales')->where('uuid', $saleUuid)->get()->getRowArray();
        if (!$sale) {
            return redirect()->to(base_url('tienda'))->with('error', 'La orden de compra no existe.');
        }

        $participant = $this->participantModel->find((int) $sale['participant_id']);

        $items = $this->db->table('sale_items si')
            ->select('t.id as ticket_id, t.code, t.price, t.status as ticket_status')
            ->join('tickets t', 't.id = si.ticket_id')
            ->where('si.sale_id', $sale['id'])
            ->get()
            ->getResultArray();

        $tIds = array_column($items, 'ticket_id');
        $numbersRows = !empty($tIds) ? $this->db->table('ticket_numbers')
            ->select('ticket_id, number')
            ->whereIn('ticket_id', $tIds)
            ->get()
            ->getResultArray() : [];

        $numbersMap = [];
        foreach ($numbersRows as $row) {
            $numbersMap[$row['ticket_id']][] = $row['number'];
        }

        foreach ($items as &$it) {
            $it['numbers'] = $numbersMap[$it['ticket_id']] ?? [];
        }
        unset($it);

        $raffle = $this->db->table('tickets t')
            ->select('r.*')
            ->join('raffles r', 'r.id = t.raffle_id')
            ->where('t.id', $items[0]['ticket_id'] ?? 0)
            ->get()
            ->getRowArray();

        $payment = $this->db->table('payments')
            ->where('sale_id', $sale['id'])
            ->orderBy('id', 'DESC')
            ->get()
            ->getRowArray();

        return view('pages/store/confirmation', [
            'title'       => 'Confirmación de Compra #' . $sale['sale_code'],
            'sale'        => $sale,
            'raffle'      => $raffle,
            'participant' => $participant,
            'items'       => $items,
            'payment'     => $payment,
        ]);
    }

    /**
     * Consulta pública de boletas compradas por un usuario.
     * GET /tienda/consultar
     */
    public function consultar()
    {
        $q = trim((string) $this->request->getGet('q'));
        $sales = [];
        $searched = false;

        if ($q !== '') {
            $searched = true;
            // Buscar participantes con ese teléfono o documento
            $participantIds = $this->db->table('participants')
                ->select('id')
                ->groupStart()
                    ->where('phone', $q)
                    ->orWhere('document_number', $q)
                ->groupEnd()
                ->get()
                ->getResultArray();

            $pIds = array_column($participantIds, 'id');

            if (!empty($pIds)) {
                $sales = $this->db->table('sales')
                    ->whereIn('participant_id', $pIds)
                    ->orderBy('id', 'DESC')
                    ->get()
                    ->getResultArray();

                foreach ($sales as &$s) {
                    $tickets = $this->db->table('sale_items si')
                        ->select('t.id, t.code, t.status, r.name as raffle_name')
                        ->join('tickets t', 't.id = si.ticket_id')
                        ->join('raffles r', 'r.id = t.raffle_id')
                        ->where('si.sale_id', $s['id'])
                        ->get()
                        ->getResultArray();

                    $tIds = array_column($tickets, 'id');
                    $nums = !empty($tIds) ? $this->db->table('ticket_numbers')->whereIn('ticket_id', $tIds)->get()->getResultArray() : [];
                    $numsMap = [];
                    foreach ($nums as $nr) {
                        $numsMap[$nr['ticket_id']][] = $nr['number'];
                    }

                    foreach ($tickets as &$tk) {
                        $tk['numbers'] = $numsMap[$tk['id']] ?? [];
                    }
                    unset($tk);

                    $s['tickets'] = $tickets;
                }
                unset($s);
            }
        }

        return view('pages/store/consultar', [
            'title'    => 'Consultar Mis Boletas - Rifas',
            'q'        => $q,
            'sales'    => $sales,
            'searched' => $searched,
        ]);
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
            mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}

