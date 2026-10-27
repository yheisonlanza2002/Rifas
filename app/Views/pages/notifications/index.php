<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
	<div class="breadcrumb-title pe-3">Administración</div>
	<div class="ps-3">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb mb-0 p-0">
				<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="bx bx-home-alt"></i></a>
				</li>
				<li class="breadcrumb-item active" aria-current="page">Notificaciones</li>
			</ol>
		</nav>
	</div>
	<?php if ($unreadCount > 0): ?>
		<div class="ms-auto">
			<a href="<?= base_url('notificaciones/marcar-todas') ?>" class="btn btn-outline-primary px-4">
				<i class="bx bx-check-double"></i> Marcar todas como leídas
			</a>
		</div>
	<?php endif; ?>
</div>

<div class="card">
	<div class="card-header">
		<div class="d-flex align-items-center justify-content-between">
			<div>
				<h6 class="mb-0">Mis Notificaciones</h6>
			</div>
			<?php if ($unreadCount > 0): ?>
				<span class="badge bg-danger"><?= $unreadCount ?> sin leer</span>
			<?php endif; ?>
		</div>
	</div>
	<div class="card-body">
		<?php if (empty($notifications)): ?>
			<div class="text-center py-4 text-muted">
				<i class="bx bx-bell-off fs-4 d-block mb-2"></i>
				No tienes notificaciones.
			</div>
		<?php else: ?>
			<div class="list-group">
				<?php foreach ($notifications as $n): ?>
					<div class="list-group-item list-group-item-action <?= $n['read_at'] ? '' : 'list-group-item-primary' ?>">
						<div class="d-flex w-100 justify-content-between">
							<h6 class="mb-1">
								<?= esc($n['type']) ?>
								<?php if (!$n['read_at']): ?>
									<span class="badge bg-danger ms-2">Nueva</span>
								<?php endif; ?>
							</h6>
							<small class="text-muted"><?= date('d/m/Y H:i', strtotime($n['created_at'])) ?></small>
						</div>
						<p class="mb-1">
							<?php
							$data = json_decode($n['data'], true);
							echo esc($data['message'] ?? $n['data']);
							?>
						</p>
						<div class="d-flex gap-2 mt-2">
							<?php if (!$n['read_at']): ?>
								<a href="<?= base_url('notificaciones/' . $n['id'] . '/marcar') ?>"
								   class="btn btn-sm btn-outline-success">
									<i class="bx bx-check"></i> Marcar como leída
								</a>
							<?php endif; ?>
							<a href="<?= base_url('notificaciones/' . $n['id'] . '/eliminar') ?>"
							   class="btn btn-sm btn-outline-danger"
							   data-confirm="¿Eliminar esta notificación?"
							   data-confirm-btn="Sí, eliminar">
								<i class="bx bx-trash"></i>
							</a>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</div>
<?= $this->endSection() ?>
