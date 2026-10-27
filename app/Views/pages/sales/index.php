<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
	<div class="breadcrumb-title pe-3">Administración</div>
	<div class="ps-3">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb mb-0 p-0">
				<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="bx bx-home-alt"></i></a>
				</li>
				<li class="breadcrumb-item active" aria-current="page">Ventas</li>
			</ol>
		</nav>
	</div>
	<div class="ms-auto">
		<a href="<?= base_url('ventas/crear') ?>" class="btn btn-primary px-4"><i class="bx bx-plus"></i> Nueva Venta</a>
	</div>
</div>

<div class="card">
	<div class="card-header">
		<div class="d-flex align-items-center justify-content-between">
			<div>
				<h6 class="mb-0">Listado de Ventas</h6>
			</div>
			<div>
				<form method="get" class="d-flex gap-2">
					<select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
						<option value="">Todos los estados</option>
						<?php foreach ($statuses as $key => $label): ?>
							<option value="<?= $key ?>" <?= $filterStatus === $key ? 'selected' : '' ?>>
								<?= esc($label) ?>
							</option>
						<?php endforeach; ?>
					</select>
				</form>
			</div>
		</div>
	</div>
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-striped table-bordered align-middle">
				<thead class="table-light">
					<tr>
						<th>#</th>
						<th>Código</th>
						<th>Participante</th>
						<th>Cantidad</th>
						<th>Total</th>
						<th>Estado</th>
						<th class="text-center">Acciones</th>
					</tr>
				</thead>
				<tbody>
					<?php if (empty($sales)): ?>
						<tr>
							<td colspan="7" class="text-center py-4 text-muted">
								<i class="bx bx-info-circle fs-4 d-block mb-2"></i>
								No hay ventas registradas.
							</td>
						</tr>
					<?php else: ?>
						<?php foreach ($sales as $s): ?>
							<tr>
								<td><?= esc($s['id']) ?></td>
								<td><span class="badge bg-light text-dark border"><?= esc($s['sale_code']) ?></span></td>
								<td>
									<div class="d-flex align-items-center gap-2">
										<div class="widgets-icons-2 rounded-circle bg-light-success text-success px-2 py-1">
											<i class="bx bx-user"></i>
										</div>
										<span class="fw-semibold"><?= esc($s['participant_name'] ?? '—') ?></span>
									</div>
								</td>
								<td><?= esc($s['quantity']) ?></td>
								<td>$ <?= number_format($s['total'], 2) ?> <?= esc($s['currency']) ?></td>
								<td>
									<?php if ($s['status'] === 'PAID'): ?>
										<span class="badge bg-success">Pagada</span>
									<?php elseif ($s['status'] === 'PENDING'): ?>
										<span class="badge bg-warning text-dark">Pendiente</span>
									<?php else: ?>
										<span class="badge bg-secondary">Cancelada</span>
									<?php endif; ?>
								</td>
								<td class="text-center">
									<div class="d-flex justify-content-center gap-2">
										<a href="<?= base_url('ventas/' . $s['id'] . '/editar') ?>" class="btn btn-sm btn-outline-primary" title="Editar">
											<i class="bx bx-edit-alt"></i>
										</a>
										<?php if ($s['status'] !== 'PAID'): ?>
											<a href="<?= base_url('ventas/' . $s['id'] . '/eliminar') ?>"
											   class="btn btn-sm btn-outline-danger"
											   title="Eliminar"
											   data-confirm="¿Eliminar la venta <?= esc($s['sale_code']) ?>?"
											   data-confirm-text="Esta acción no se puede deshacer"
											   data-confirm-btn="Sí, eliminar">
												<i class="bx bx-trash"></i>
											</a>
										<?php endif; ?>
									</div>
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
