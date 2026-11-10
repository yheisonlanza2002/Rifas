<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- PAGE HEADER -->
<div class="d-flex align-items-center mb-4">
    <div>
        <h5 class="mb-1 fw-bold">Dashboard</h5>
        <p class="mb-0 text-secondary">Bienvenido al panel de control — <?= date('d \d\e F \d\e Y') ?></p>
    </div>
    <div class="ms-auto">
        <a href="<?= base_url('ventas/crear') ?>" class="btn btn-primary px-4">
            <i class='bx bx-plus me-1'></i> Nueva Venta
        </a>
    </div>
</div>

<!-- ============================================================ -->
<!-- KPI CARDS -->
<!-- ============================================================ -->
<div class="row g-3 mb-4">

    <!-- Ventas del mes -->
    <div class="col-6 col-xl-3">
        <div class="card radius-10 border-start border-4 border-primary h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-1 text-secondary small">Ventas del Mes</p>
                        <h4 class="mb-0 fw-bold">$ <?= number_format($ventasMes, 0, ',', '.') ?></h4>
                        <small class="text-muted"><?= $numVentasMes ?> transacciones</small>
                    </div>
                    <div class="ms-auto">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class='bx bx-dollar-circle text-primary fs-2'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ventas del día -->
    <div class="col-6 col-xl-3">
        <div class="card radius-10 border-start border-4 border-success h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-1 text-secondary small">Ventas de Hoy</p>
                        <h4 class="mb-0 fw-bold">$ <?= number_format($ventasHoy, 0, ',', '.') ?></h4>
                        <small class="text-muted"><?= date('d/m/Y') ?></small>
                    </div>
                    <div class="ms-auto">
                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                            <i class='bx bx-trending-up text-success fs-2'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Boletas vendidas -->
    <div class="col-6 col-xl-3">
        <div class="card radius-10 border-start border-4 border-warning h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-1 text-secondary small">Boletas Vendidas</p>
                        <h4 class="mb-0 fw-bold"><?= number_format($boletasVendidas) ?></h4>
                        <small class="text-muted"><?= number_format($boletasDisponibles) ?> disponibles</small>
                    </div>
                    <div class="ms-auto">
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class='bx bx-ticket text-warning fs-2'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Participantes -->
    <div class="col-6 col-xl-3">
        <div class="card radius-10 border-start border-4 border-info h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-1 text-secondary small">Participantes</p>
                        <h4 class="mb-0 fw-bold"><?= number_format($totalParticipantes) ?></h4>
                        <small class="<?= $pagosPendientes > 0 ? 'text-warning' : 'text-muted' ?>">
                            <?php if ($pagosPendientes > 0): ?>
                                <i class='bx bx-error-circle me-1'></i><?= $pagosPendientes ?> pagos pendientes
                            <?php else: ?>
                                Sin pagos pendientes
                            <?php endif; ?>
                        </small>
                    </div>
                    <div class="ms-auto">
                        <div class="bg-info bg-opacity-10 rounded-circle p-3">
                            <i class='bx bx-user-pin text-info fs-2'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ============================================================ -->
<!-- GRÁFICO DE VENTAS + DONUT DE BOLETAS -->
<!-- ============================================================ -->
<div class="row g-3 mb-4">

    <!-- Gráfico de ventas por día -->
    <div class="col-12 col-xl-8">
        <div class="card radius-10 h-100">
            <div class="card-header bg-transparent">
                <div class="d-flex align-items-center">
                    <h6 class="mb-0 fw-semibold"><i class='bx bx-line-chart me-2 text-primary'></i>Ventas últimos 30 días</h6>
                    <a href="<?= base_url('reportes') ?>" class="btn btn-sm btn-outline-secondary ms-auto">Ver reporte</a>
                </div>
            </div>
            <div class="card-body">
                <div id="chart-ventas-30dias"></div>
            </div>
        </div>
    </div>

    <!-- Donut distribución boletas -->
    <div class="col-12 col-xl-4">
        <div class="card radius-10 h-100">
            <div class="card-header bg-transparent">
                <h6 class="mb-0 fw-semibold"><i class='bx bx-pie-chart-alt-2 me-2 text-warning'></i>Estado de Boletas</h6>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                <div id="chart-boletas-donut" class="w-100"></div>
                <div class="d-flex flex-wrap gap-3 justify-content-center mt-2">
                    <div class="text-center">
                        <span class="badge bg-success me-1">&nbsp;</span>
                        <small>Disponible <strong><?= number_format($donutData['AVAILABLE']) ?></strong></small>
                    </div>
                    <div class="text-center">
                        <span class="badge bg-primary me-1">&nbsp;</span>
                        <small>Vendida <strong><?= number_format($donutData['SOLD']) ?></strong></small>
                    </div>
                    <div class="text-center">
                        <span class="badge bg-warning me-1">&nbsp;</span>
                        <small>Reservada <strong><?= number_format($donutData['RESERVED']) ?></strong></small>
                    </div>
                    <div class="text-center">
                        <span class="badge bg-danger me-1">&nbsp;</span>
                        <small>Cancelada <strong><?= number_format($donutData['CANCELLED']) ?></strong></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ============================================================ -->
<!-- TOP RIFAS + ÚLTIMAS VENTAS -->
<!-- ============================================================ -->
<div class="row g-3">

    <!-- Top rifas -->
    <div class="col-12 col-xl-5">
        <div class="card radius-10 h-100">
            <div class="card-header bg-transparent">
                <div class="d-flex align-items-center">
                    <h6 class="mb-0 fw-semibold"><i class='bx bx-gift me-2 text-danger'></i>Top Rifas</h6>
                    <a href="<?= base_url('rifas') ?>" class="btn btn-sm btn-outline-secondary ms-auto">Ver todas</a>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (empty($topRifas)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class='bx bx-inbox fs-1'></i>
                        <p class="mt-2">No hay rifas aún</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Rifa</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-end">Vendidas</th>
                                    <th class="text-end">%</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($topRifas as $r): ?>
                                    <?php
                                        $pct = $r['total'] > 0 ? round(($r['vendidas'] / $r['total']) * 100) : 0;
                                        $badgeClass = match($r['status']) {
                                            'ACTIVE' => 'bg-success',
                                            'DRAFT'  => 'bg-secondary',
                                            'CLOSED' => 'bg-dark',
                                            default  => 'bg-secondary',
                                        };
                                        $badgeLabel = match($r['status']) {
                                            'ACTIVE' => 'Activa',
                                            'DRAFT'  => 'Borrador',
                                            'CLOSED' => 'Cerrada',
                                            default  => $r['status'],
                                        };
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold"><?= esc($r['name']) ?></div>
                                            <small class="text-muted"><?= esc($r['code']) ?> · <?= $r['total'] ?> boletas</small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge <?= $badgeClass ?>"><?= $badgeLabel ?></span>
                                        </td>
                                        <td class="text-end fw-semibold"><?= number_format($r['vendidas']) ?></td>
                                        <td class="text-end">
                                            <div class="d-flex align-items-center justify-content-end gap-2">
                                                <div class="progress flex-grow-1" style="height:6px;width:60px;">
                                                    <div class="progress-bar bg-primary" style="width:<?= $pct ?>%"></div>
                                                </div>
                                                <small><?= $pct ?>%</small>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Últimas ventas -->
    <div class="col-12 col-xl-7">
        <div class="card radius-10 h-100">
            <div class="card-header bg-transparent">
                <div class="d-flex align-items-center">
                    <h6 class="mb-0 fw-semibold"><i class='bx bx-cart-alt me-2 text-success'></i>Últimas Ventas</h6>
                    <a href="<?= base_url('ventas') ?>" class="btn btn-sm btn-outline-secondary ms-auto">Ver todas</a>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (empty($ultimasVentas)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class='bx bx-inbox fs-1'></i>
                        <p class="mt-2">No hay ventas aún</p>
                        <a href="<?= base_url('ventas/crear') ?>" class="btn btn-primary btn-sm">Registrar primera venta</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Código</th>
                                    <th>Participante</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-end">Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ultimasVentas as $v): ?>
                                    <?php
                                        $badgeClass = match($v['status']) {
                                            'PAID'      => 'bg-success',
                                            'PENDING'   => 'bg-warning text-dark',
                                            'CANCELLED' => 'bg-danger',
                                            default     => 'bg-secondary',
                                        };
                                        $badgeLabel = match($v['status']) {
                                            'PAID'      => 'Pagada',
                                            'PENDING'   => 'Pendiente',
                                            'CANCELLED' => 'Cancelada',
                                            default     => $v['status'],
                                        };
                                    ?>
                                    <tr>
                                        <td><code class="text-primary"><?= esc($v['sale_code']) ?></code></td>
                                        <td>
                                            <div><?= esc(trim($v['participante'])) ?></div>
                                            <small class="text-muted"><?= esc($v['phone'] ?? '') ?></small>
                                        </td>
                                        <td class="text-end fw-semibold">$ <?= number_format((float)$v['total'], 0, ',', '.') ?></td>
                                        <td class="text-center">
                                            <span class="badge <?= $badgeClass ?>"><?= $badgeLabel ?></span>
                                        </td>
                                        <td class="text-end text-muted small">
                                            <?= date('d/m/Y', strtotime($v['created_at'])) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<!-- Accesos rápidos -->
<div class="row g-3 mt-1">
    <div class="col-12">
        <div class="card radius-10 border-0 bg-primary bg-opacity-10">
            <div class="card-body py-3">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <span class="fw-semibold text-primary me-2"><i class='bx bx-zap me-1'></i>Accesos rápidos:</span>
                    <a href="<?= base_url('rifas/crear') ?>" class="btn btn-sm btn-primary"><i class='bx bx-gift me-1'></i>Nueva Rifa</a>
                    <a href="<?= base_url('ventas/crear') ?>" class="btn btn-sm btn-success"><i class='bx bx-cart-alt me-1'></i>Nueva Venta</a>
                    <a href="<?= base_url('participantes/crear') ?>" class="btn btn-sm btn-info text-white"><i class='bx bx-user-plus me-1'></i>Nuevo Participante</a>
                    <a href="<?= base_url('sorteos/crear') ?>" class="btn btn-sm btn-warning text-dark"><i class='bx bx-dice-5 me-1'></i>Crear Sorteo</a>
                    <a href="<?= base_url('pasarelas') ?>" class="btn btn-sm btn-outline-secondary"><i class='bx bx-wallet-alt me-1'></i>Pasarelas</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Gráfico de ventas por día ──────────────────────────────
    var optionsVentas = {
        series: [{
            name: 'Ventas ($)',
            data: <?= json_encode($diasData) ?>
        }],
        chart: {
            type: 'area',
            height: 280,
            toolbar: { show: false },
            zoom: { enabled: false }
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2 },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.05,
                stops: [0, 90, 100]
            }
        },
        xaxis: {
            categories: <?= json_encode($diasLabels) ?>,
            tickAmount: 10,
            labels: { style: { fontSize: '11px' } }
        },
        yaxis: {
            labels: {
                formatter: function(val) {
                    return '$ ' + (val >= 1000000 ? (val/1000000).toFixed(1) + 'M' : val >= 1000 ? (val/1000).toFixed(0) + 'K' : val);
                }
            }
        },
        colors: ['#0d6efd'],
        tooltip: {
            y: {
                formatter: function(val) {
                    return '$ ' + val.toLocaleString('es-CO');
                }
            }
        },
        grid: { borderColor: '#e0e0e0' }
    };
    new ApexCharts(document.querySelector('#chart-ventas-30dias'), optionsVentas).render();

    // ── Donut distribución de boletas ──────────────────────────
    var donutData = <?= json_encode(array_values($donutData)) ?>;
    var optionsDonut = {
        series: donutData,
        labels: ['Disponible', 'Vendida', 'Reservada', 'Cancelada'],
        chart: {
            type: 'donut',
            height: 220,
        },
        colors: ['#198754', '#0d6efd', '#ffc107', '#dc3545'],
        legend: { show: false },
        dataLabels: {
            formatter: function(val, opts) {
                return opts.w.globals.series[opts.seriesIndex];
            }
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '65%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total',
                            formatter: function(w) {
                                return w.globals.seriesTotals.reduce((a, b) => a + b, 0).toLocaleString('es-CO');
                            }
                        }
                    }
                }
            }
        },
        tooltip: {
            y: { formatter: function(val) { return val.toLocaleString('es-CO') + ' boletas'; } }
        }
    };
    new ApexCharts(document.querySelector('#chart-boletas-donut'), optionsDonut).render();
});
</script>

<?= $this->endSection() ?>
