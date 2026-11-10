<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
	<div class="breadcrumb-title pe-3">Administración</div>
	<div class="ps-3">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb mb-0 p-0">
				<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="bx bx-home-alt"></i></a>
				</li>
				<li class="breadcrumb-item active" aria-current="page">Pagos</li>
			</ol>
		</nav>
	</div>
</div>

<p class="text-muted mb-3">
	<i class="bx bx-info-circle me-1"></i>
	Reporte de recaudos: los pagos se registran desde el módulo <a href="<?= base_url('ventas') ?>">Ventas</a>.
</p>

<!-- Tarjetas resumen -->
<div class="row g-3 mb-3">
	<div class="col-xl-3 col-md-6">
		<div class="card">
			<div class="card-body d-flex align-items-center gap-3">
				<div class="widgets-icons-2 rounded-circle bg-light-primary text-primary px-3 py-2">
					<i class="bx bx-wallet fs-4"></i>
				</div>
				<div>
					<small class="text-muted text-uppercase">Total recaudado</small>
					<div class="fw-bold fs-5">$ <?= number_format($recaudado, 2) ?></div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xl-3 col-md-6">
		<div class="card">
			<div class="card-body d-flex align-items-center gap-3">
				<div class="widgets-icons-2 rounded-circle bg-light-success text-success px-3 py-2">
					<i class="bx bx-money fs-4"></i>
				</div>
				<div>
					<small class="text-muted text-uppercase">Efectivo</small>
					<div class="fw-bold fs-5">$ <?= number_format($efectivo, 2) ?></div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xl-3 col-md-6">
		<div class="card">
			<div class="card-body d-flex align-items-center gap-3">
				<div class="widgets-icons-2 rounded-circle bg-light-info text-info px-3 py-2">
					<i class="bx bx-transfer-alt fs-4"></i>
				</div>
				<div>
					<small class="text-muted text-uppercase">Transferencias</small>
					<div class="fw-bold fs-5">$ <?= number_format($transferencias, 2) ?></div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xl-3 col-md-6">
		<div class="card">
			<div class="card-body d-flex align-items-center gap-3">
				<div class="widgets-icons-2 rounded-circle bg-light-warning text-warning px-3 py-2">
					<i class="bx bx-hourglass fs-4"></i>
				</div>
				<div>
					<small class="text-muted text-uppercase">Pendiente por cobrar</small>
					<div class="fw-bold fs-5">$ <?= number_format($pendienteCobrar, 2) ?></div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="card">
	<div class="card-header">
		<div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
			<h6 class="mb-0">Movimientos de pago <span class="badge bg-light text-dark border"><?= (int) $totalPagos ?></span></h6>
			<form method="get" class="d-flex gap-2 flex-wrap">
				<input type="text" name="q" class="form-control form-control-sm" placeholder="Venta, referencia, participante..."
					   value="<?= esc($filterQ) ?>" style="width: 200px;">
				<input type="date" name="desde" class="form-control form-control-sm" value="<?= esc($filterDesde) ?>" title="Desde">
				<input type="date" name="hasta" class="form-control form-control-sm" value="<?= esc($filterHasta) ?>" title="Hasta">
				<select name="method" class="form-select form-select-sm" style="width: 150px;">
					<option value="">Todos los métodos</option>
					<?php foreach ($methods as $key => $label): ?>
						<option value="<?= esc($key) ?>" <?= $filterMethod === $key ? 'selected' : '' ?>><?= esc($label) ?></option>
					<?php endforeach; ?>
				</select>
				<select name="status" class="form-select form-select-sm" style="width: 150px;">
					<option value="">Todos los estados</option>
					<?php foreach ($statuses as $key => $label): ?>
						<option value="<?= esc($key) ?>" <?= $filterStatus === $key ? 'selected' : '' ?>><?= esc($label) ?></option>
					<?php endforeach; ?>
				</select>
				<button type="submit" class="btn btn-sm btn-outline-secondary"><i class="bx bx-filter"></i></button>
				<?php if ($filterDesde || $filterHasta || $filterStatus || $filterMethod || $filterQ): ?>
					<a href="<?= base_url('pagos') ?>" class="btn btn-sm btn-outline-danger" title="Limpiar filtros"><i class="bx bx-x"></i></a>
				<?php endif; ?>
			</form>
		</div>
	</div>
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-striped table-bordered align-middle">
				<thead class="table-light">
					<tr>
						<th>Fecha</th>
						<th>Venta</th>
						<th>Participante</th>
						<th>Método</th>
						<th>Referencia</th>
						<th class="text-end">Monto</th>
						<th>Estado</th>
					</tr>
				</thead>
				<tbody>
					<?php if (empty($payments)): ?>
						<tr>
							<td colspan="7" class="text-center py-4 text-muted">
								<i class="bx bx-info-circle fs-4 d-block mb-2"></i>
								No hay pagos registrados.
							</td>
						</tr>
					<?php else: ?>
						<?php foreach ($payments as $p): ?>
							<?php
								$nombre = trim(($p['participant_name'] ?? '') . ' ' . ($p['participant_last_name'] ?? ''));
								$esTransfer = ($p['payment_method'] ?? '') === 'TRANSFER';
							?>
							<tr>
								<td>
									<small><?= esc(date('d/m/Y H:i', strtotime($p['created_at']))) ?></small>
								</td>
								<td>
									<span class="badge bg-light text-dark border"><?= esc($p['sale_code'] ?? '—') ?></span>
								</td>
								<td class="fw-semibold"><?= esc($nombre ?: '—') ?></td>
								<td>
									<?php if ($esTransfer): ?>
										<span class="badge bg-info-subtle text-info"><i class="bx bx-transfer-alt me-1"></i>Transferencia</span>
									<?php else: ?>
										<span class="badge bg-success-subtle text-success"><i class="bx bx-money me-1"></i>Efectivo</span>
									<?php endif; ?>
								</td>
								<td>
									<?php if (!empty($p['transaction_reference'])): ?>
										<code><?= esc($p['transaction_reference']) ?></code>
									<?php else: ?>
										<span class="text-muted">—</span>
									<?php endif; ?>
								</td>
								<td class="text-end fw-semibold">$ <?= number_format((float) $p['amount'], 2) ?></td>
								<td>
									<?php
										$estados = [
											'COMPLETED' => ['bg-success', 'Completado'],
											'PENDING'   => ['bg-warning text-dark', 'Pendiente'],
											'FAILED'    => ['bg-danger', 'Fallido'],
											'REFUNDED'  => ['bg-secondary', 'Reembolsado'],
										];
										$e = $estados[$p['status']] ?? ['bg-secondary', $p['status']];
									?>
									<span class="badge <?= $e[0] ?>"><?= $e[1] ?></span>
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
