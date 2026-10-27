<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
	<div class="breadcrumb-title pe-3">Administración</div>
	<div class="ps-3">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb mb-0 p-0">
				<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="bx bx-home-alt"></i></a>
				</li>
				<li class="breadcrumb-item"><a href="<?= base_url('sorteos') ?>">Sorteos</a></li>
				<li class="breadcrumb-item active" aria-current="page">Resultados</li>
			</ol>
		</nav>
	</div>
</div>

<div class="card">
	<div class="card-header">
		<div class="d-flex align-items-center justify-content-between">
			<div>
				<h6 class="mb-0">Resultados: <?= esc($draw['name']) ?></h6>
				<small class="text-muted">
					Rifa: <?= esc($draw['raffle_name'] ?? '—') ?> |
					<?= $methods[$draw['method']] ?? '' ?> |
					<?= date('d/m/Y H:i', strtotime($draw['executed_at'])) ?>
				</small>
			</div>
			<?php if ($draw['result_hash']): ?>
				<span class="badge bg-light text-dark border" title="<?= esc($draw['result_hash']) ?>">
					<i class="bx bx-hash"></i> <?= substr($draw['result_hash'], 0, 12) ?>...
				</span>
			<?php endif; ?>
		</div>
	</div>
	<div class="card-body">
		<?php if (empty($winners)): ?>
			<div class="text-center py-4 text-muted">
				<i class="bx bx-info-circle fs-4 d-block mb-2"></i>
				No hay ganadores registrados.
			</div>
		<?php else: ?>
			<div class="table-responsive">
				<table class="table table-striped table-bordered align-middle">
					<thead class="table-light">
						<tr>
							<th>#</th>
							<th>Premio</th>
							<th>Boleta</th>
							<th>Número</th>
							<th>Participante</th>
							<th>Estado</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($winners as $w): ?>
							<tr>
								<td><?= esc($w['id']) ?></td>
								<td>
									<span class="badge bg-warning text-dark">
										<?= esc($prizeMap[$w['prize_id']]['name'] ?? '—') ?>
									</span>
								</td>
								<td><?= esc($w['ticket_id']) ?></td>
								<td><?= esc($w['ticket_number_id']) ?></td>
								<td>
									<?php if (isset($participantMap[$w['participant_id']])): ?>
										<?= esc($participantMap[$w['participant_id']]['name']) ?>
										<br>
										<small class="text-muted"><?= esc($participantMap[$w['participant_id']]['document_number']) ?></small>
									<?php else: ?>
										<span class="text-muted">—</span>
									<?php endif; ?>
								</td>
								<td>
									<?php if ($w['status'] === 'PENDING'): ?>
										<span class="badge bg-warning">Pendiente</span>
									<?php elseif ($w['status'] === 'ANNOUNCED'): ?>
										<span class="badge bg-info">Anunciado</span>
									<?php elseif ($w['status'] === 'CLAIMED'): ?>
										<span class="badge bg-success">Reclamado</span>
									<?php else: ?>
										<span class="badge bg-secondary">Expirado</span>
									<?php endif; ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		<?php endif; ?>
	</div>
</div>

<div class="mt-3">
	<a href="<?= base_url('sorteos') ?>" class="btn btn-outline-secondary px-4">
		<i class="bx bx-arrow-back"></i> Volver a Sorteos
	</a>
</div>
<?= $this->endSection() ?>
