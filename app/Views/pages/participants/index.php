<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
	<div class="breadcrumb-title pe-3">Administración</div>
	<div class="ps-3">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb mb-0 p-0">
				<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="bx bx-home-alt"></i></a>
				</li>
				<li class="breadcrumb-item active" aria-current="page">Participantes</li>
			</ol>
		</nav>
	</div>
	<div class="ms-auto">
		<a href="<?= base_url('participantes/crear') ?>" class="btn btn-primary px-4"><i class="bx bx-plus"></i> Nuevo Participante</a>
	</div>
</div>
<!--end breadcrumb-->

<div class="card">
	<div class="card-header">
		<div class="d-flex align-items-center">
			<div>
				<h6 class="mb-0">Listado de Participantes</h6>
			</div>
		</div>
	</div>
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-striped table-bordered align-middle">
				<thead class="table-light">
					<tr>
						<th>#</th>
						<th>Documento</th>
						<th>Nombre</th>
						<th>Teléfono</th>
						<th>Email</th>
						<th>Ciudad</th>
						<th>Estado</th>
						<th class="text-center">Acciones</th>
					</tr>
				</thead>
				<tbody>
					<?php if (empty($participants)): ?>
						<tr>
							<td colspan="8" class="text-center py-4 text-muted">
								<i class="bx bx-info-circle fs-4 d-block mb-2"></i>
								No hay participantes registrados.
							</td>
						</tr>
					<?php else: ?>
						<?php foreach ($participants as $p): ?>
							<tr>
								<td><?= esc($p['id']) ?></td>
								<td>
									<span class="badge bg-light text-dark border"><?= esc($p['document_type']) ?></span>
									<?= esc($p['document_number']) ?>
								</td>
								<td>
									<div class="d-flex align-items-center gap-2">
										<div class="widgets-icons-2 rounded-circle bg-light-success text-success px-2 py-1">
											<i class="bx bx-user"></i>
										</div>
										<span class="fw-semibold"><?= esc(trim($p['name'] . ' ' . ($p['last_name'] ?? ''))) ?></span>
									</div>
								</td>
								<td><?= esc($p['phone']) ?></td>
								<td><?= esc($p['email'] ?? '—') ?></td>
								<td><?= esc($p['city'] ?? '—') ?></td>
								<td>
									<?php if ($p['status'] === 'ACTIVE'): ?>
										<span class="badge bg-success">Activo</span>
									<?php else: ?>
										<span class="badge bg-danger"><?= esc($p['status']) ?></span>
									<?php endif; ?>
								</td>
								<td class="text-center">
									<div class="d-flex justify-content-center gap-2">
										<a href="<?= base_url('participantes/' . $p['id'] . '/editar') ?>" class="btn btn-sm btn-outline-primary" title="Editar">
											<i class="bx bx-edit-alt"></i>
										</a>
										<a href="<?= base_url('participantes/' . $p['id'] . '/eliminar') ?>"
										   class="btn btn-sm btn-outline-danger"
										   title="Eliminar"
										   data-confirm="¿Eliminar a <?= esc(trim($p['name'] . ' ' . ($p['last_name'] ?? ''))) ?>?"
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
