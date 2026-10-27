<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
	<div class="breadcrumb-title pe-3">Administración</div>
	<div class="ps-3">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb mb-0 p-0">
				<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="bx bx-home-alt"></i></a>
				</li>
				<li class="breadcrumb-item active" aria-current="page">Pasarelas de Pago</li>
			</ol>
		</nav>
	</div>
	<div class="ms-auto">
		<a href="<?= base_url('pasarelas/crear') ?>" class="btn btn-primary px-4"><i class="bx bx-plus"></i> Nueva Pasarela</a>
	</div>
</div>

<div class="card">
	<div class="card-header">
		<div class="d-flex align-items-center">
			<div>
				<h6 class="mb-0">Listado de Pasarelas de Pago</h6>
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
						<th>Slug</th>
						<th>Driver</th>
						<th>Modo</th>
						<th>Estado</th>
						<th class="text-center">Acciones</th>
					</tr>
				</thead>
				<tbody>
					<?php if (empty($gateways)): ?>
						<tr>
							<td colspan="7" class="text-center py-4 text-muted">
								<i class="bx bx-info-circle fs-4 d-block mb-2"></i>
								No hay pasarelas registradas.
							</td>
						</tr>
					<?php else: ?>
						<?php foreach ($gateways as $g): ?>
							<tr>
								<td><?= esc($g['id']) ?></td>
								<td>
									<div class="d-flex align-items-center gap-2">
										<div class="widgets-icons-2 rounded-circle bg-light-info text-info px-2 py-1">
											<i class="bx bx-credit-card"></i>
										</div>
										<span class="fw-semibold"><?= esc($g['name']) ?></span>
									</div>
								</td>
								<td><span class="badge bg-light text-dark border"><?= esc($g['slug']) ?></span></td>
								<td><?= esc($g['driver']) ?></td>
								<td>
									<?php if ($g['test_mode']): ?>
										<span class="badge bg-warning text-dark">Prueba</span>
									<?php else: ?>
										<span class="badge bg-success">Producción</span>
									<?php endif; ?>
								</td>
								<td>
									<?php if ($g['status'] === 'ACTIVE'): ?>
										<span class="badge bg-success">Activo</span>
									<?php else: ?>
										<span class="badge bg-secondary">Inactivo</span>
									<?php endif; ?>
								</td>
								<td class="text-center">
									<div class="d-flex justify-content-center gap-2">
										<a href="<?= base_url('pasarelas/' . $g['id'] . '/editar') ?>" class="btn btn-sm btn-outline-primary" title="Editar">
											<i class="bx bx-edit-alt"></i>
										</a>
										<a href="<?= base_url('pasarelas/' . $g['id'] . '/eliminar') ?>"
										   class="btn btn-sm btn-outline-danger"
										   title="Eliminar"
										   data-confirm="¿Eliminar la pasarela <?= esc($g['name']) ?>?"
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
