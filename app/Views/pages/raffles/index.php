<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
	<div class="breadcrumb-title pe-3">Administración</div>
	<div class="ps-3">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb mb-0 p-0">
				<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="bx bx-home-alt"></i></a>
				</li>
				<li class="breadcrumb-item active" aria-current="page">Rifas</li>
			</ol>
		</nav>
	</div>
	<div class="ms-auto">
		<a href="<?= base_url('rifas/crear') ?>" class="btn btn-primary px-4"><i class="bx bx-plus"></i> Nueva Rifa</a>
	</div>
</div>

<div class="card">
	<div class="card-header">
		<div class="d-flex align-items-center">
			<div>
				<h6 class="mb-0">Listado de Rifas</h6>
			</div>
		</div>
	</div>
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-striped table-bordered align-middle">
				<thead class="table-light">
					<tr>
						<th>#</th>
						<th>Nombre</th>
						<th>Código</th>
						<th>Boletas</th>
						<th>Precio</th>
						<th>Estado</th>
						<th>Fecha Inicio</th>
						<th class="text-center">Acciones</th>
					</tr>
				</thead>
				<tbody>
					<?php if (empty($raffles)): ?>
						<tr>
							<td colspan="8" class="text-center py-4 text-muted">
								<i class="bx bx-info-circle fs-4 d-block mb-2"></i>
								No hay rifas registradas.
							</td>
						</tr>
					<?php else: ?>
						<?php foreach ($raffles as $r): ?>
							<tr>
								<td><?= esc($r['id']) ?></td>
								<td>
									<div class="d-flex align-items-center gap-2">
										<div class="widgets-icons-2 rounded-circle bg-light-primary text-primary px-2 py-1">
											<i class="bx bx-gift"></i>
										</div>
										<span class="fw-semibold"><?= esc($r['name']) ?></span>
									</div>
								</td>
								<td><span class="badge bg-light text-dark border"><?= esc($r['code']) ?></span></td>
								<td><?= number_format($r['ticket_quantity']) ?></td>
								<td>$ <?= number_format($r['ticket_price'], 2) ?> <?= esc($r['currency']) ?></td>
								<td>
									<?php if ($r['status'] === 'ACTIVE'): ?>
										<span class="badge bg-success">Activa</span>
									<?php elseif ($r['status'] === 'DRAFT'): ?>
										<span class="badge bg-warning text-dark">Borrador</span>
									<?php else: ?>
										<span class="badge bg-secondary">Cerrada</span>
									<?php endif; ?>
								</td>
								<td><?= $r['start_at'] ? date('d/m/Y H:i', strtotime($r['start_at'])) : '—' ?></td>
								<td class="text-center">
									<div class="d-flex justify-content-center gap-2">
										<a href="<?= base_url('rifas/' . $r['id'] . '/editar') ?>" class="btn btn-sm btn-outline-primary" title="Editar">
											<i class="bx bx-edit-alt"></i>
										</a>
										<a href="<?= base_url('rifas/' . $r['id'] . '/eliminar') ?>"
										   class="btn btn-sm btn-outline-danger"
										   title="Eliminar"
										   data-confirm="¿Eliminar la rifa <?= esc($r['name']) ?>?"
										   data-confirm-text="Esta acción no se puede deshacer"
										   data-confirm-btn="Sí, eliminar">
											<i class="bx bx-trash"></i>
										</a>
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
