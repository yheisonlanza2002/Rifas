<?php

namespace App\Controllers;

use App\Libraries\WompiLibrary;
use App\Models\PaymentGatewayModel;
use App\Models\PaymentModel;
use App\Models\SaleModel;
use CodeIgniter\Controller;

class Webhook extends Controller
{
    protected $db;
    protected SaleModel $saleModel;
    protected PaymentModel $paymentModel;
    protected PaymentGatewayModel $gatewayModel;

    public function __construct()
    {
        $this->db           = \Config\Database::connect();
        $this->saleModel    = new SaleModel();
        $this->paymentModel = new PaymentModel();
        $this->gatewayModel = new PaymentGatewayModel();
    }

    /**
     * Endpoint público para recibir webhooks de Wompi.
     * POST /webhook/wompi
     */
    public function wompi()
    {
        $rawBody = (string) $this->request->getBody();
        $payload = json_decode($rawBody, true);

        if (!is_array($payload) || empty($payload['data']['transaction'])) {
            return $this->response->setStatusCode(400)->setJSON([
                'ok'      => false,
                'message' => 'Payload inválido o vacío.',
            ]);
        }

        $transaction = $payload['data']['transaction'];
        $reference   = trim((string) ($transaction['reference'] ?? ''));
        $txStatus    = strtoupper((string) ($transaction['status'] ?? ''));
        $txId        = (string) ($transaction['id'] ?? '');

        // 1. Buscar la venta por código de venta o UUID
        $sale = $this->db->table('sales')
            ->where('sale_code', $reference)
            ->orWhere('uuid', $reference)
            ->get()
            ->getRowArray();

        if (!$sale) {
            $this->logWebhook(null, 'wompi', $payload['event'] ?? 'unknown', $payload, 'FAILED', 'Venta no encontrada para la referencia: ' . $reference);
            return $this->response->setStatusCode(404)->setJSON([
                'ok'      => false,
                'message' => 'Venta no encontrada para la referencia ' . $reference,
            ]);
        }

        $companyId = (int) $sale['company_id'];

        // 2. Buscar pasarela Wompi configurada para la empresa
        $gateway = $this->gatewayModel
            ->forCompany($companyId)
            ->where('driver', 'wompi')
            ->where('status', 'ACTIVE')
            ->first();

        // 3. Validar firma del webhook si la pasarela tiene llave de eventos
        $eventsKey = '';
        if ($gateway && !empty($gateway['config'])) {
            $config = json_decode($gateway['config'], true);
            $eventsKey = $config['events_key'] ?? '';
        }

        if (!empty($eventsKey)) {
            $isValid = WompiLibrary::verifyWebhookSignature($payload, $eventsKey);
            if (!$isValid) {
                $this->logWebhook($gateway['id'] ?? null, 'wompi', $payload['event'] ?? 'unknown', $payload, 'FAILED', 'Firma de webhook inválida');
                return $this->response->setStatusCode(401)->setJSON([
                    'ok'      => false,
                    'message' => 'Firma inválida.',
                ]);
            }
        }

        // 4. Si la venta ya está pagada (idempotencia)
        if ($sale['status'] === 'PAID') {
            $this->logWebhook($gateway['id'] ?? null, 'wompi', $payload['event'] ?? 'unknown', $payload, 'PROCESSED', 'Venta ya pagada previamente');
            return $this->response->setJSON([
                'ok'      => true,
                'message' => 'La venta ya se encuentra pagada.',
            ]);
        }

        // 5. Procesar según estado de la transacción
        if ($txStatus === 'APPROVED') {
            $this->db->transBegin();
            try {
                // Actualizar venta
                $this->db->table('sales')->where('id', $sale['id'])->update([
                    'status'         => 'PAID',
                    'paid_amount'    => $sale['total'],
                    'pending_amount' => 0,
                    'sold_at'        => date('Y-m-d H:i:s'),
                    'updated_at'     => date('Y-m-d H:i:s'),
                ]);

                // Obtener IDs de tickets de esta venta
                $items = $this->db->table('sale_items')->where('sale_id', $sale['id'])->get()->getResultArray();
                $ticketIds = array_column($items, 'ticket_id');

                if (!empty($ticketIds)) {
                    $this->db->table('tickets')
                        ->whereIn('id', $ticketIds)
                        ->update([
                            'status'  => 'SOLD',
                            'sold_at' => date('Y-m-d H:i:s'),
                        ]);

                    // Registrar eventos de boletas
                    $events = [];
                    foreach ($ticketIds as $tId) {
                        $events[] = [
                            'uuid'        => $this->generateUuid(),
                            'ticket_id'   => (int) $tId,
                            'event_type'  => 'SOLD',
                            'description' => 'Boleta vendida automáticamente vía Webhook Wompi (Ref: ' . $txId . ')',
                            'created_at'  => date('Y-m-d H:i:s'),
                        ];
                    }
                    $this->db->table('ticket_events')->insertBatch($events);
                }

                // Registrar en tabla payments
                $this->db->table('payments')->insert([
                    'uuid'                  => $this->generateUuid(),
                    'company_id'            => $companyId,
                    'sale_id'               => (int) $sale['id'],
                    'gateway_id'            => $gateway ? (int) $gateway['id'] : null,
                    'amount'                => (float) $sale['total'],
                    'fee_amount'            => 0,
                    'net_amount'            => (float) $sale['total'],
                    'payment_method'        => $transaction['payment_method_type'] ?? 'WOMPI',
                    'status'                => 'COMPLETED',
                    'transaction_reference' => $txId,
                    'gateway'               => 'wompi',
                    'raw_response'          => json_encode($transaction),
                    'paid_at'               => date('Y-m-d H:i:s'),
                    'created_at'            => date('Y-m-d H:i:s'),
                ]);

                $this->db->transCommit();
                $this->logWebhook($gateway['id'] ?? null, 'wompi', $payload['event'] ?? 'unknown', $payload, 'PROCESSED');

                return $this->response->setJSON([
                    'ok'      => true,
                    'message' => 'Venta procesada y marcada como pagada correctamente.',
                ]);
            } catch (\Throwable $e) {
                $this->db->transRollback();
                $this->logWebhook($gateway['id'] ?? null, 'wompi', $payload['event'] ?? 'unknown', $payload, 'FAILED', $e->getMessage());

                return $this->response->setStatusCode(500)->setJSON([
                    'ok'      => false,
                    'message' => 'Error al procesar el pago: ' . $e->getMessage(),
                ]);
            }
        } elseif (in_array($txStatus, ['DECLINED', 'VOIDED', 'ERROR'], true)) {
            // Liberar boletas si estaban reservadas
            $items = $this->db->table('sale_items')->where('sale_id', $sale['id'])->get()->getResultArray();
            $ticketIds = array_column($items, 'ticket_id');

            if (!empty($ticketIds)) {
                $this->db->table('tickets')
                    ->whereIn('id', $ticketIds)
                    ->where('status', 'RESERVED')
                    ->update(['status' => 'AVAILABLE']);
            }

            $this->db->table('sales')->where('id', $sale['id'])->update([
                'status'     => 'CANCELLED',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $this->logWebhook($gateway['id'] ?? null, 'wompi', $payload['event'] ?? 'unknown', $payload, 'PROCESSED', 'Transacción ' . $txStatus);

            return $this->response->setJSON([
                'ok'      => true,
                'message' => 'Transacción rechazada/cancelada: ' . $txStatus,
            ]);
        }

        // Otros estados (ej. PENDING)
        $this->logWebhook($gateway['id'] ?? null, 'wompi', $payload['event'] ?? 'unknown', $payload, 'RECEIVED', 'Estado ' . $txStatus);

        return $this->response->setJSON([
            'ok'      => true,
            'message' => 'Evento recibido con estado: ' . $txStatus,
        ]);
    }

    /**
     * Guarda el registro en la tabla webhooks para auditoría.
     */
    private function logWebhook(?int $gatewayId, string $gateway, string $eventType, array $payload, string $status, ?string $errorMessage = null): void
    {
        try {
            $this->db->table('webhooks')->insert([
                'uuid'          => $this->generateUuid(),
                'gateway_id'    => $gatewayId,
                'gateway'       => $gateway,
                'event_type'    => $eventType,
                'payload'       => json_encode($payload),
                'headers'       => json_encode($this->request->getHeaders()),
                'status'        => $status,
                'error_message' => $errorMessage,
                'processed_at'  => $status === 'PROCESSED' ? date('Y-m-d H:i:s') : null,
                'created_at'    => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Error registrando webhook: ' . $e->getMessage());
        }
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

