<?php

namespace App\Controllers;

use App\Models\PaymentModel;

class Payments extends BaseController
{
    protected PaymentModel $paymentModel;

    private const STATUSES = [
        'PENDING'   => 'Pendiente',
        'COMPLETED' => 'Completado',
        'FAILED'    => 'Fallido',
        'REFUNDED'  => 'Reembolsado',
    ];

    private const METHODS = [
        'CASH'     => 'Efectivo',
        'TRANSFER' => 'Transferencia',
    ];

    public function __construct()
    {
        $this->paymentModel = new PaymentModel();
    }

    /**
     * GET /pagos — Reportes de recaudos (solo lectura)
     */
    public function index(): string
    {
        $companyId = (int) session()->get('company_id');
        $status    = $this->request->getGet('status') ?? '';
        $method    = $this->request->getGet('method') ?? '';
        $desde     = $this->request->getGet('desde') ?? '';
        $hasta     = $this->request->getGet('hasta') ?? '';
        $q         = trim((string) ($this->request->getGet('q') ?? ''));

        // ----- Tarjetas resumen (recaudos) -----
        $baseSum = $this->db->table('payments')->where('company_id', $companyId);
        if ($desde !== '') {
            $baseSum->where('DATE(created_at) >=', $desde);
        }
        if ($hasta !== '') {
            $baseSum->where('DATE(created_at) <=', $hasta);
        }

        $recaudado = (clone $baseSum)
            ->where('status', 'COMPLETED')
            ->select('COALESCE(SUM(amount),0) AS monto', false)
            ->get()->getRow()->monto;

        $efectivo = (clone $baseSum)
            ->where('status', 'COMPLETED')
            ->where('payment_method', 'CASH')
            ->select('COALESCE(SUM(amount),0) AS monto', false)
            ->get()->getRow()->monto;

        $transferencias = (clone $baseSum)
            ->where('status', 'COMPLETED')
            ->where('payment_method', 'TRANSFER')
            ->select('COALESCE(SUM(amount),0) AS monto', false)
            ->get()->getRow()->monto;

        $pendienteCobrar = (float) $this->db->table('sales')
            ->select('COALESCE(SUM(pending_amount),0) AS monto', false)
            ->where('company_id', $companyId)
            ->where('status', 'PENDING')
            ->get()->getRow()->monto;

        // ----- Listado de pagos -----
        $builder = $this->paymentModel
            ->forCompany($companyId)
            ->select('participants.name AS participant_name', false)
            ->select('participants.last_name AS participant_last_name', false)
            ->join('participants', 'participants.id = sales.participant_id')
            ->orderBy('payments.created_at', 'DESC');

        if ($status !== '' && array_key_exists($status, self::STATUSES)) {
            $builder->where('payments.status', $status);
        }
        if ($method !== '' && array_key_exists($method, self::METHODS)) {
            $builder->where('payments.payment_method', $method);
        }
        if ($desde !== '') {
            $builder->where('DATE(payments.created_at) >=', $desde);
        }
        if ($hasta !== '') {
            $builder->where('DATE(payments.created_at) <=', $hasta);
        }
        if ($q !== '') {
            $builder->groupStart()
                ->like('sales.sale_code', $q)
                ->orLike('transaction_reference', $q)
                ->orLike('participants.name', $q)
                ->groupEnd();
        }

        $payments = $builder->findAll();

        $totalPagos = count($payments);

        return view('pages/payments/index', [
            'title'         => 'Pagos - Rifas',
            'payments'      => $payments,
            'statuses'      => self::STATUSES,
            'methods'       => self::METHODS,
            'filterStatus'  => $status,
            'filterMethod'  => $method,
            'filterDesde'   => $desde,
            'filterHasta'   => $hasta,
            'filterQ'       => $q,
            'recaudado'     => (float) $recaudado,
            'efectivo'      => (float) $efectivo,
            'transferencias'=> (float) $transferencias,
            'pendienteCobrar'=> $pendienteCobrar,
            'totalPagos'    => $totalPagos,
        ]);
    }
}
