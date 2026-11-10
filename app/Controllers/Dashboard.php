<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index(): string
    {
        $companyId = (int) (session()->get('company_id') ?: 1);
        $db = \Config\Database::connect();

        // =============================================
        // KPIs: Ventas del día, semana y mes
        // =============================================
        $today     = date('Y-m-d');
        $weekStart = date('Y-m-d', strtotime('monday this week'));
        $monthStart= date('Y-m-01');

        // Ventas totales del mes (monto)
        $ventasMes = (float) $db->table('sales')
            ->selectSum('total', 'total')
            ->where('company_id', $companyId)
            ->where('status', 'PAID')
            ->where('DATE(sold_at) >=', $monthStart)
            ->get()->getRowArray()['total'] ?? 0;

        // Ventas del día
        $ventasHoy = (float) $db->table('sales')
            ->selectSum('total', 'total')
            ->where('company_id', $companyId)
            ->where('status', 'PAID')
            ->where('DATE(sold_at)', $today)
            ->get()->getRowArray()['total'] ?? 0;

        // Conteo de ventas del mes
        $numVentasMes = (int) $db->table('sales')
            ->where('company_id', $companyId)
            ->where('status', 'PAID')
            ->where('DATE(sold_at) >=', $monthStart)
            ->countAllResults();

        // Boletas vendidas (total)
        $boletasVendidas = (int) $db->table('tickets')
            ->where('company_id', $companyId)
            ->where('status', 'SOLD')
            ->countAllResults();

        // Boletas disponibles
        $boletasDisponibles = (int) $db->table('tickets')
            ->where('company_id', $companyId)
            ->where('status', 'AVAILABLE')
            ->countAllResults();

        // Rifas activas
        $rifasActivas = (int) $db->table('raffles')
            ->where('company_id', $companyId)
            ->where('status', 'ACTIVE')
            ->where('deleted_at', null)
            ->countAllResults();

        // Total participantes activos
        $totalParticipantes = (int) $db->table('participants')
            ->where('company_id', $companyId)
            ->where('status', 'ACTIVE')
            ->where('deleted_at', null)
            ->countAllResults();

        // Pagos pendientes
        $pagosPendientes = (int) $db->table('sales')
            ->where('company_id', $companyId)
            ->where('status', 'PENDING')
            ->countAllResults();

        // =============================================
        // Gráfico: Ventas por día (últimos 30 días)
        // =============================================
        $fechaInicio30 = date('Y-m-d', strtotime('-29 days'));
        $ventasPorDia = $db->query("
            SELECT DATE(sold_at) AS dia, SUM(total) AS total, COUNT(*) AS cantidad
            FROM sales
            WHERE company_id = {$companyId}
              AND status = 'PAID'
              AND sold_at >= '{$fechaInicio30}'
            GROUP BY DATE(sold_at)
            ORDER BY dia ASC
        ")->getResultArray();

        // Preparar datos para el gráfico (rellenar días vacíos)
        $diasLabels = [];
        $diasData   = [];
        for ($i = 29; $i >= 0; $i--) {
            $fecha = date('Y-m-d', strtotime("-{$i} days"));
            $diasLabels[] = date('d/m', strtotime($fecha));
            $diasData[$fecha] = 0;
        }
        foreach ($ventasPorDia as $row) {
            if (isset($diasData[$row['dia']])) {
                $diasData[$row['dia']] = (float) $row['total'];
            }
        }

        // =============================================
        // Top 5 rifas por boletas vendidas
        // =============================================
        $topRifas = $db->query("
            SELECT r.name, r.code, r.status,
                   COUNT(CASE WHEN t.status = 'SOLD' THEN 1 END) AS vendidas,
                   COUNT(t.id) AS total,
                   r.ticket_price
            FROM raffles r
            LEFT JOIN tickets t ON t.raffle_id = r.id AND t.company_id = r.company_id
            WHERE r.company_id = {$companyId}
              AND r.deleted_at IS NULL
            GROUP BY r.id
            ORDER BY vendidas DESC
            LIMIT 5
        ")->getResultArray();

        // =============================================
        // Últimas 10 ventas
        // =============================================
        $ultimasVentas = $db->query("
            SELECT s.sale_code, s.total, s.status, s.created_at, s.sold_at,
                   CONCAT(p.name, ' ', IFNULL(p.last_name, '')) AS participante,
                   p.phone
            FROM sales s
            JOIN participants p ON p.id = s.participant_id
            WHERE s.company_id = {$companyId}
            ORDER BY s.id DESC
            LIMIT 10
        ")->getResultArray();

        // =============================================
        // Distribución de estados de boletas (para donut)
        // =============================================
        $estadosBoletas = $db->query("
            SELECT status, COUNT(*) AS total
            FROM tickets
            WHERE company_id = {$companyId}
            GROUP BY status
        ")->getResultArray();

        $donutData = ['AVAILABLE' => 0, 'SOLD' => 0, 'RESERVED' => 0, 'CANCELLED' => 0];
        foreach ($estadosBoletas as $row) {
            if (isset($donutData[$row['status']])) {
                $donutData[$row['status']] = (int) $row['total'];
            }
        }

        return view('pages/dashboard/index', [
            'title'              => 'Dashboard - Rifas',
            'plugins_css'        => [
                'plugins/apexcharts-bundle/css/apexcharts.css',
            ],
            'plugins_js'         => [
                'plugins/apexcharts-bundle/js/apexcharts.min.js',
            ],

            // KPIs
            'ventasMes'          => $ventasMes,
            'ventasHoy'          => $ventasHoy,
            'numVentasMes'       => $numVentasMes,
            'boletasVendidas'    => $boletasVendidas,
            'boletasDisponibles' => $boletasDisponibles,
            'rifasActivas'       => $rifasActivas,
            'totalParticipantes' => $totalParticipantes,
            'pagosPendientes'    => $pagosPendientes,

            // Gráficos
            'diasLabels'         => $diasLabels,
            'diasData'           => array_values($diasData),
            'donutData'          => $donutData,

            // Tablas
            'topRifas'           => $topRifas,
            'ultimasVentas'      => $ultimasVentas,
        ]);
    }
}
