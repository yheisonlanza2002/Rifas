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
				<li class="breadcrumb-item active" aria-current="page">Usuarios</li>
			</ol>
		</nav>
	</div>
	<div class="ms-auto">
		<a href="<?= base_url('usuarios/crear') ?>" class="btn btn-primary px-4"><i class="bx bx-plus"></i> Nuevo Usuario</a>
	</div>
</div>
<!--end breadcrumb-->

<div class="card">
	<div class="card-header">
		<div class="d-flex align-items-center">
			<div>
				<h6 class="mb-0">Listado de Usuarios</h6>
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
						<th>Email</th>
						<th>Teléfono</th>
						<th>Roles</th>
						<th>Estado</th>
						<th>Último acceso</th>
						<th class="text-center">Acciones</th>
					</tr>
				</thead>
				<tbody>
					<?php if (empty($users)): ?>
						<tr>
							<td colspan="8" class="text-center py-4 text-muted">
								<i class="bx bx-info-circle fs-4 d-block mb-2"></i>
								No hay usuarios registrados.
							</td>
						</tr>
					<?php else: ?>
						<?php foreach ($users as $user): ?>
							<tr>
								<td><?= esc($user['id']) ?></td>
								<td>
									<div class="d-flex align-items-center gap-2">
										<div class="widgets-icons-2 rounded-circle bg-light-primary text-primary px-2 py-1">
											<i class="bx bx-user"></i>
										</div>
										<span class="fw-semibold"><?= esc($user['name'] . ' ' . ($user['last_name'] ?? '')) ?></span>
									</div>
								</td>
								<td><?= esc($user['email']) ?></td>
								<td><?= esc($user['phone'] ?? '—') ?></td>
								<td>
									<?php
										$badgeColors = ['SUPER_ADMIN' => 'bg-danger', 'ADMIN' => 'bg-primary', 'SELLER' => 'bg-success', 'VIEWER' => 'bg-secondary'];
										$rolesList = !empty($user['role_names']) ? explode(', ', $user['role_names']) : [];
									?>
									<?php if (empty($rolesList)): ?>
										<span class="badge bg-light text-dark">Sin rol</span>
									<?php else: ?>
										<?php foreach ($rolesList as $roleName): ?>
											<span class="badge <?= $badgeColors[$roleName] ?? 'bg-info' ?>"><?= esc($roleName) ?></span>
										<?php endforeach; ?>
									<?php endif; ?>
								</td>
								<td>
									<?php if ($user['status'] === 'ACTIVE'): ?>
										<span class="badge bg-success">Activo</span>
									<?php else: ?>
										<span class="badge bg-danger"><?= esc($user['status']) ?></span>
									<?php endif; ?>
								</td>
								<td>
									<?= $user['last_login_at'] ? esc(date('d/m/Y H:i', strtotime($user['last_login_at']))) : 'Nunca' ?>
								</td>
								<td class="text-center">
									<div class="d-flex justify-content-center gap-2">
										<a href="<?= base_url('usuarios/' . $user['id'] . '/editar') ?>" class="btn btn-sm btn-outline-primary" title="Editar">
											<i class="bx bx-edit-alt"></i>
										</a>
										<?php if ((int) session()->get('user_id') !== (int) $user['id']): ?>
											<a href="<?= base_url('usuarios/' . $user['id'] . '/eliminar') ?>"
											   class="btn btn-sm btn-outline-danger"
											   title="Eliminar"
											   data-confirm="¿Eliminar a <?= esc($user['name']) ?>?"
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
