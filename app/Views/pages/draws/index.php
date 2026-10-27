<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
	<div class="breadcrumb-title pe-3">Administración</div>
	<div class="ps-3">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb mb-0 p-0">
				<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="bx bx-home-alt"></i></a>
				</li>
				<li class="breadcrumb-item active" aria-current="page">Sorteos</li>
			</ol>
		</nav>
	</div>
	<div class="ms-auto">
		<a href="<?= base_url('sorteos/crear') ?>" class="btn btn-primary px-4"><i class="bx bx-plus"></i> Nuevo Sorteo</a>
	</div>
</div>

<div class="card">
	<div class="card-header">
		<div class="d-flex align-items-center">
			<div>
				<h6 class="mb-0">Listado de Sorteos</h6>
			</div>
		</div>
	</div>
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-striped table-bordered align-middle">
				<thead class="table-light">
					<tr>
						<th>#</th>
						<th>Sorteo</th>
						<th>Rifa</th>
						<th>Fecha Programada</th>
						<th>Método</th>
						<th>Estado</th>
						<th class="text-center">Acciones</th>
					</tr>
				</thead>
				<tbody>
					<?php if (empty($draws)): ?>
						<tr>
							<td colspan="7" class="text-center py-4 text-muted">
								<i class="bx bx-info-circle fs-4 d-block mb-2"></i>
								No hay sorteos registrados.
							</td>
						</tr>
					<?php else: ?>
						<?php foreach ($draws as $d): ?>
							<tr>
								<td><?= esc($d['id']) ?></td>
								<td>
									<div class="d-flex align-items-center gap-2">
										<div class="widgets-icons-2 rounded-circle bg-info text-white px-2 py-1">
											<i class="bx bx-trophy"></i>
										</div>
										<span class="fw-semibold"><?= esc($d['name']) ?></span>
									</div>
								</td>
								<td><?= esc($d['raffle_name'] ?? '—') ?></td>
								<td><?= date('d/m/Y H:i', strtotime($d['scheduled_at'])) ?></td>
								<td>
									<span class="badge bg-light text-dark border">
										<?= $methods[$d['method']] ?? esc($d['method']) ?>
									</span>
								</td>
								<td>
									<?php if ($d['status'] === 'SCHEDULED'): ?>
										<span class="badge bg-warning">Programado</span>
									<?php elseif ($d['status'] === 'EXECUTING'): ?>
										<span class="badge bg-info">Ejecutando</span>
									<?php elseif ($d['status'] === 'COMPLETED'): ?>
										<span class="badge bg-success">Completado</span>
									<?php else: ?>
										<span class="badge bg-secondary">Cancelado</span>
									<?php endif; ?>
								</td>
								<td class="text-center">
									<div class="d-flex justify-content-center gap-2">
										<?php if ($d['status'] === 'SCHEDULED'): ?>
											<a href="<?= base_url('sorteos/' . $d['id'] . '/editar') ?>"
											   class="btn btn-sm btn-outline-primary" title="Editar">
												<i class="bx bx-edit-alt"></i>
											</a>
											<a href="<?= base_url('sorteos/' . $d['id'] . '/ejecutar') ?>"
											   class="btn btn-sm btn-outline-success"
											   title="Ejecutar Sorteo"
											   data-confirm="¿Ejecutar el sorteo <?= esc($d['name']) ?>?"
											   data-confirm-text="Se seleccionarán ganadores aleatoriamente"
											   data-confirm-btn="Sí, ejecutar">
												<i class="bx bx-play"></i>
											</a>
											<a href="<?= base_url('sorteos/' . $d['id'] . '/cancelar') ?>"
											   class="btn btn-sm btn-outline-warning"
											   title="Cancelar"
											   data-confirm="¿Cancelar el sorteo <?= esc($d['name']) ?>?"
											   data-confirm-text="Esta acción no se puede deshacer"
											   data-confirm-btn="Sí, cancelar">
												<i class="bx bx-x"></i>
											</a>
											<a href="<?= base_url('sorteos/' . $d['id'] . '/eliminar') ?>"
											   class="btn btn-sm btn-outline-danger"
											   title="Eliminar"
											   data-confirm="¿Eliminar el sorteo <?= esc($d['name']) ?>?"
											   data-confirm-text="Esta acción no se puede deshacer"
											   data-confirm-btn="Sí, eliminar">
												<i class="bx bx-trash"></i>
											</a>
										<?php elseif ($d['status'] === 'COMPLETED'): ?>
											<a href="<?= base_url('sorteos/' . $d['id'] . '/resultados') ?>"
											   class="btn btn-sm btn-outline-info" title="Ver Resultados">
												<i class="bx bx-list-ol"></i>
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
