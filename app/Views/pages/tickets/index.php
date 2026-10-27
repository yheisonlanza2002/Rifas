<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
	<div class="breadcrumb-title pe-3">Administración</div>
	<div class="ps-3">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb mb-0 p-0">
				<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="bx bx-home-alt"></i></a>
				</li>
				<li class="breadcrumb-item active" aria-current="page">Boletas</li>
			</ol>
		</nav>
	</div>
	<div class="ms-auto">
		<a href="<?= base_url('boletas/crear') ?>" class="btn btn-primary px-4"><i class="bx bx-plus"></i> Nueva Boleta</a>
	</div>
</div>

<div class="card">
	<div class="card-header">
		<div class="d-flex align-items-center justify-content-between">
			<div>
				<h6 class="mb-0">Listado de Boletas</h6>
			</div>
			<div>
				<form method="get" class="d-flex gap-2">
					<select name="raffle_id" class="form-select form-select-sm" onchange="this.form.submit()">
						<option value="">Todas las rifas</option>
						<?php foreach ($raffles as $r): ?>
							<option value="<?= esc($r['id']) ?>" <?= $filterRaffleId == $r['id'] ? 'selected' : '' ?>>
								<?= esc($r['name']) ?>
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
						<th>Rifa</th>
						<th>Precio</th>
						<th>Números</th>
						<th>Estado</th>
						<th class="text-center">Acciones</th>
					</tr>
				</thead>
				<tbody>
					<?php if (empty($tickets)): ?>
						<tr>
							<td colspan="7" class="text-center py-4 text-muted">
								<i class="bx bx-info-circle fs-4 d-block mb-2"></i>
								No hay boletas registradas.
							</td>
						</tr>
					<?php else: ?>
						<?php foreach ($tickets as $t): ?>
							<tr>
								<td><?= esc($t['id']) ?></td>
								<td><span class="badge bg-light text-dark border"><?= esc($t['code']) ?></span></td>
								<td><?= esc($t['raffle_name'] ?? '—') ?></td>
								<td>$ <?= number_format($t['price'], 2) ?></td>
								<td><?= esc($t['number_quantity']) ?></td>
								<td>
									<?php if ($t['status'] === 'AVAILABLE'): ?>
										<span class="badge bg-success">Disponible</span>
									<?php elseif ($t['status'] === 'RESERVED'): ?>
										<span class="badge bg-warning text-dark">Reservado</span>
									<?php elseif ($t['status'] === 'SOLD'): ?>
										<span class="badge bg-primary">Vendido</span>
									<?php else: ?>
										<span class="badge bg-secondary">Cancelado</span>
									<?php endif; ?>
								</td>
								<td class="text-center">
									<div class="d-flex justify-content-center gap-2">
										<a href="<?= base_url('boletas/' . $t['id'] . '/editar') ?>" class="btn btn-sm btn-outline-primary" title="Editar">
											<i class="bx bx-edit-alt"></i>
										</a>
										<?php if ($t['status'] !== 'SOLD'): ?>
											<a href="<?= base_url('boletas/' . $t['id'] . '/eliminar') ?>"
											   class="btn btn-sm btn-outline-danger"
											   title="Eliminar"
											   data-confirm="¿Eliminar la boleta <?= esc($t['code']) ?>?"
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
