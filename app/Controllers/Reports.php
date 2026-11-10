<?php

namespace App\Controllers;

use App\Models\RaffleModel;

class Reports extends BaseController
{
    protected $db;
    protected RaffleModel $raffleModel;

    public function __construct()
    {
        $this->db          = \Config\Database::connect();
        $this->raffleModel = new RaffleModel();
    }

    /**
     * Pantalla principal de reportes y estadísticas.
     * GET /reportes
     */
    public function index(): string
    {
        $companyId = (int) session()->get('company_id');

        // Filtros
        $dateFrom = $this->request->getGet('date_from') ?: date('Y-m-01');
        $dateTo   = $this->request->getGet('date_to') ?: date('Y-m-d');
        $raffleId = (int) $this->request->getGet('raffle_id');
        $status   = $this->request->getGet('status') ?: '';

        // Listado de rifas para el select
        $raffles = $this->raffleModel
            ->forCompany($companyId)
            ->orderBy('name', 'ASC')
            ->findAll();

        // 1. Consulta base de ventas en el período
        $salesBuilder = $this->db->table('sales s')
            ->select("
                s.id, s.sale_code, s.total, s.quantity, s.status, s.created_at, s.sold_at,
                CONCAT(p.name, ' ', IFNULL(p.last_name, '')) as participant_name,
                p.phone, p.document_number,
                (SELECT r.name FROM sale_items si 
                 JOIN tickets t ON t.id = si.ticket_id 
                 JOIN raffles r ON r.id = t.raffle_id 
                 WHERE si.sale_id = s.id LIMIT 1) as raffle_name,
                (SELECT GROUP_CONCAT(tn.number SEPARATOR ', ') FROM sale_items si 
                 JOIN ticket_numbers tn ON tn.ticket_id = si.ticket_id 
                 WHERE si.sale_id = s.id) as ticket_numbers
            ")
            ->join('participants p', 'p.id = s.participant_id')
            ->where('s.company_id', $companyId)
            ->where('DATE(s.created_at) >=', $dateFrom)
            ->where('DATE(s.created_at) <=', $dateTo);

        if ($status !== '') {
            $salesBuilder->where('s.status', $status);
        }

        if ($raffleId > 0) {
            $salesBuilder->where("EXISTS (
                SELECT 1 FROM sale_items si 
                JOIN tickets t ON t.id = si.ticket_id 
                WHERE si.sale_id = s.id AND t.raffle_id = {$raffleId}
            )");
        }

        $sales = $salesBuilder->orderBy('s.id', 'DESC')->get()->getResultArray();

        // 2. KPIs del período (solo ventas pagadas para recaudación)
        $kpiBuilder = $this->db->table('sales s')
            ->select("
                COUNT(*) as total_sales,
                SUM(CASE WHEN s.status = 'PAID' THEN s.total ELSE 0 END) as total_revenue,
                SUM(CASE WHEN s.status = 'PAID' THEN s.quantity ELSE 0 END) as total_tickets_sold,
                SUM(CASE WHEN s.status = 'PENDING' THEN 1 ELSE 0 END) as pending_sales
            ")
            ->where('s.company_id', $companyId)
            ->where('DATE(s.created_at) >=', $dateFrom)
            ->where('DATE(s.created_at) <=', $dateTo);

        if ($raffleId > 0) {
            $kpiBuilder->where("EXISTS (
                SELECT 1 FROM sale_items si 
                JOIN tickets t ON t.id = si.ticket_id 
                WHERE si.sale_id = s.id AND t.raffle_id = {$raffleId}
            )");
        }

        $kpis = $kpiBuilder->get()->getRowArray();
        $totalRevenue = (float) ($kpis['total_revenue'] ?? 0);
        $totalSales   = (int) ($kpis['total_sales'] ?? 0);
        $ticketsSold  = (int) ($kpis['total_tickets_sold'] ?? 0);
        $averageSale  = ($totalSales > 0 && $totalRevenue > 0) ? round($totalRevenue / $totalSales, 2) : 0;

        // 3. Resumen por Rifa (rendimiento global de cada rifa)
        $rafflesSummary = $this->db->query("
            SELECT r.id, r.name, r.code, r.status, r.ticket_price,
                   COUNT(t.id) as total_tickets,
                   SUM(CASE WHEN t.status = 'SOLD' THEN 1 ELSE 0 END) as sold_tickets,
                   SUM(CASE WHEN t.status = 'AVAILABLE' THEN 1 ELSE 0 END) as available_tickets,
                   IFNULL((
                       SELECT SUM(s.total) 
                       FROM sales s 
                       JOIN sale_items si ON si.sale_id = s.id 
                       JOIN tickets t2 ON t2.id = si.ticket_id 
                       WHERE t2.raffle_id = r.id AND s.status = 'PAID'
                   ), 0) as total_collected
            FROM raffles r
            LEFT JOIN tickets t ON t.raffle_id = r.id AND t.company_id = r.company_id
            WHERE r.company_id = {$companyId} AND r.deleted_at IS NULL
            GROUP BY r.id
            ORDER BY sold_tickets DESC
        ")->getResultArray();

        // 4. Resumen por Método de Pago en el período
        $paymentsSummary = $this->db->query("
            SELECT IFNULL(payment_method, 'SIN REGISTRO') as method,
                   COUNT(*) as count,
                   SUM(amount) as total_amount
            FROM payments
            WHERE company_id = {$companyId}
              AND DATE(created_at) >= '{$dateFrom}'
              AND DATE(created_at) <= '{$dateTo}'
              AND status = 'COMPLETED'
            GROUP BY payment_method
            ORDER BY total_amount DESC
        ")->getResultArray();

        return view('pages/reports/index', [
            'title'           => 'Reportes y Estadísticas - Rifas',
            'dateFrom'        => $dateFrom,
            'dateTo'          => $dateTo,
            'raffleId'        => $raffleId,
            'status'          => $status,
            'raffles'         => $raffles,
            'sales'           => $sales,
            'totalRevenue'    => $totalRevenue,
            'totalSales'      => $totalSales,
            'ticketsSold'     => $ticketsSold,
            'averageSale'     => $averageSale,
            'rafflesSummary'  => $rafflesSummary,
            'paymentsSummary' => $paymentsSummary,
        ]);
    }
}

