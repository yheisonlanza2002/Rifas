<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- BREADCRUMB & ACCIONES -->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Reportes</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">Estadísticas y Ventas</li>
            </ol>
        </nav>
    </div>
    <div class="ms-auto d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary px-3" onclick="window.print()">
            <i class="bx bx-printer me-1"></i> Imprimir Reporte
        </button>
    </div>
</div>

<!-- FILTROS DE CONSULTA -->
<div class="card mb-4 shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold text-dark"><i class='bx bx-filter-alt me-1 text-primary'></i>Filtros de Búsqueda</h6>
    </div>
    <div class="card-body">
        <form action="<?= base_url('reportes') ?>" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Fecha Desde</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="<?= esc($dateFrom) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Fecha Hasta</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="<?= esc($dateTo) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Filtrar por Rifa</label>
                <select name="raffle_id" class="form-select form-select-sm">
                    <option value="">— Todas las Rifas —</option>
                    <?php foreach ($raffles as $rf): ?>
                        <option value="<?= $rf['id'] ?>" <?= $raffleId == $rf['id'] ? 'selected' : '' ?>>
                            <?= esc($rf['name']) ?> (<?= esc($rf['code']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Estado de Venta</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">— Todos —</option>
                    <option value="PAID" <?= $status === 'PAID' ? 'selected' : '' ?>>Pagadas</option>
                    <option value="PENDING" <?= $status === 'PENDING' ? 'selected' : '' ?>>Pendientes</option>
                    <option value="CANCELLED" <?= $status === 'CANCELLED' ? 'selected' : '' ?>>Canceladas</option>
                </select>
            </div>
            <div class="col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm w-100" title="Aplicar Filtros">
                    <i class='bx bx-search'></i>
                </button>
                <a href="<?= base_url('reportes') ?>" class="btn btn-outline-secondary btn-sm" title="Limpiar Filtros">
                    <i class='bx bx-reset'></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================ -->
<!-- KPI CARDS -->
<!-- ============================================================ -->
<div class="row g-3 mb-4">
    <!-- Recaudación Total -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card radius-10 border-start border-4 border-success h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-1 text-secondary small text-uppercase fw-semibold">Recaudación Pagada</p>
                        <h4 class="mb-0 fw-bold text-success">$ <?= number_format($totalRevenue, 0, ',', '.') ?></h4>
                        <small class="text-muted">En el período seleccionado</small>
                    </div>
                    <div class="ms-auto">
                        <div class="bg-success bg-opacity-10 text-success rounded-circle p-3">
                            <i class='bx bx-dollar-circle fs-2'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Boletas Vendidas -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card radius-10 border-start border-4 border-primary h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-1 text-secondary small text-uppercase fw-semibold">Boletas Vendidas</p>
                        <h4 class="mb-0 fw-bold text-primary"><?= number_format($ticketsSold, 0, ',', '.') ?></h4>
                        <small class="text-muted">Boletas confirmadas</small>
                    </div>
                    <div class="ms-auto">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3">
                            <i class='bx bx-ticket fs-2'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Órdenes / Ventas -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card radius-10 border-start border-4 border-info h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-1 text-secondary small text-uppercase fw-semibold">Transacciones</p>
                        <h4 class="mb-0 fw-bold text-info"><?= number_format($totalSales, 0, ',', '.') ?></h4>
                        <small class="text-muted">Órdenes generadas</small>
                    </div>
                    <div class="ms-auto">
                        <div class="bg-info bg-opacity-10 text-info rounded-circle p-3">
                            <i class='bx bx-cart-alt fs-2'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Promedio de Venta -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card radius-10 border-start border-4 border-warning h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-1 text-secondary small text-uppercase fw-semibold">Ticket Promedio</p>
                        <h4 class="mb-0 fw-bold text-warning">$ <?= number_format($averageSale, 0, ',', '.') ?></h4>
                        <small class="text-muted">Por transacción pagada</small>
                    </div>
                    <div class="ms-auto">
                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-3">
                            <i class='bx bx-trending-up fs-2'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- RESUMEN POR RIFA -->
    <div class="col-12 col-lg-8">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold text-dark"><i class='bx bx-gift me-2 text-primary'></i>Rendimiento Global por Rifa</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>Rifa</th>
                                <th>Precio Boleta</th>
                                <th>Progreso de Venta</th>
                                <th class="text-end">Recaudación</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($rafflesSummary)): ?>
                                <tr><td colspan="4" class="text-center py-4 text-muted">No hay rifas registradas.</td></tr>
                            <?php else: ?>
                                <?php foreach ($rafflesSummary as $rs): ?>
                                    <?php
                                    $totalT = (int) $rs['total_tickets'];
                                    $soldT  = (int) $rs['sold_tickets'];
                                    $pct    = $totalT > 0 ? round(($soldT / $totalT) * 100) : 0;
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold text-dark"><?= esc($rs['name']) ?></div>
                                            <small class="text-muted">#<?= esc($rs['code']) ?></small>
                                        </td>
                                        <td>$ <?= number_format((float) $rs['ticket_price'], 0, ',', '.') ?></td>
                                        <td style="min-width: 170px;">
                                            <div class="d-flex justify-content-between small text-muted mb-1">
                                                <span><?= $soldT ?> / <?= $totalT ?></span>
                                                <span class="fw-bold"><?= $pct ?>%</span>
                                            </div>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-primary" role="progressbar" style="width: <?= $pct ?>%"></div>
                                            </div>
                                        </td>
                                        <td class="text-end fw-bold text-success">
                                            $ <?= number_format((float) $rs['total_collected'], 0, ',', '.') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- RESUMEN POR MÉTODO DE PAGO -->
    <div class="col-12 col-lg-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold text-dark"><i class='bx bx-wallet me-2 text-success'></i>Métodos de Pago (Período)</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>Método</th>
                                <th>Cantidad</th>
                                <th class="text-end">Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($paymentsSummary)): ?>
                                <tr><td colspan="3" class="text-center py-4 text-muted">No hay pagos completados en el período.</td></tr>
                            <?php else: ?>
                                <?php foreach ($paymentsSummary as $ps): ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-light text-dark border fw-semibold">
                                                <?= esc($ps['method']) ?>
                                            </span>
                                        </td>
                                        <td><?= esc($ps['count']) ?> pagos</td>
                                        <td class="text-end fw-bold text-dark">
                                            $ <?= number_format((float) $ps['total_amount'], 0, ',', '.') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DETALLE DE VENTAS DEL PERÍODO -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
        <h6 class="mb-0 fw-bold text-dark"><i class='bx bx-list-ul me-2 text-primary'></i>Detalle de Transacciones (<?= count($sales) ?>)</h6>
        <small class="text-muted">Del <?= date('d/m/Y', strtotime($dateFrom)) ?> al <?= date('d/m/Y', strtotime($dateTo)) ?></small>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th>Código</th>
                        <th>Fecha</th>
                        <th>Participante</th>
                        <th>Teléfono</th>
                        <th>Rifa</th>
                        <th>Boletas / Números</th>
                        <th class="text-end">Monto</th>
                        <th class="text-center">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($sales)): ?>
                        <tr><td colspan="8" class="text-center py-4 text-muted">No se encontraron ventas para los filtros seleccionados.</td></tr>
                    <?php else: ?>
                        <?php foreach ($sales as $s): ?>
                            <tr>
                                <td class="fw-bold font-13"><?= esc($s['sale_code']) ?></td>
                                <td class="small text-muted"><?= date('d/m/Y h:i A', strtotime($s['created_at'])) ?></td>
                                <td class="fw-semibold text-dark"><?= esc($s['participant_name']) ?></td>
                                <td class="small text-muted"><?= esc($s['phone'] ?? 'N/A') ?></td>
                                <td class="small"><?= esc($s['raffle_name'] ?? 'N/A') ?></td>
                                <td class="small">
                                    <span class="badge bg-primary me-1"><?= $s['quantity'] ?> boleta(s)</span>
                                    <?php if (!empty($s['ticket_numbers'])): ?>
                                        <span class="text-muted font-11">[<?= esc($s['ticket_numbers']) ?>]</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end fw-bold text-dark">
                                    $ <?= number_format((float) $s['total'], 0, ',', '.') ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($s['status'] === 'PAID'): ?>
                                        <span class="badge bg-success">Pagada</span>
                                    <?php elseif ($s['status'] === 'PENDING'): ?>
                                        <span class="badge bg-warning text-dark">Pendiente</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><?= esc($s['status']) ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

