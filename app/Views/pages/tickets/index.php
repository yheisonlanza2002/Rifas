<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/tickets/tickets.css') ?>">

<div class="d-flex justify-content-between align-items-center mb-4">
	<div>
		<h4 class="mb-1">Boletas</h4>
		<p class="text-muted mb-0">
			<?= (int) $totales['total'] ?> boletas en <?= count($grupos) ?> <?= count($grupos) === 1 ? 'rifa' : 'rifas' ?>
			· <?= (int) $totales['SOLD'] ?> vendidas
			· <?= (int) $totales['AVAILABLE'] ?> disponibles
			<?php if ((int) $totales['RESERVED'] > 0): ?>· <?= (int) $totales['RESERVED'] ?> reservadas<?php endif; ?>
		</p>
	</div>
	<div class="d-flex gap-2">
		<form method="get" class="d-flex align-items-center gap-2">
			<select name="raffle_id" class="form-select form-select-sm" onchange="this.form.submit()">
				<option value="">Todas las rifas</option>
				<?php foreach ($raffles as $r): ?>
					<option value="<?= esc($r['id']) ?>" <?= $filterRaffleId == $r['id'] ? 'selected' : '' ?>>
						<?= esc($r['name']) ?>
					</option>
				<?php endforeach; ?>
			</select>
		</form>
		<a href="<?= base_url('boletas/crear') ?>" class="btn btn-primary px-4"><i class="bx bx-plus"></i> Nueva Boleta</a>
	</div>
</div>

<?php if (empty($grupos)): ?>
	<div class="text-center py-5 text-muted">
		<i class="bx bx-info-circle fs-1 d-block mb-3"></i>
		<p>No hay boletas registradas.</p>
		<a href="<?= base_url('boletas/crear') ?>" class="btn btn-primary"><i class="bx bx-plus"></i> Crear la primera boleta</a>
	</div>
<?php else: ?>
	<?php foreach ($grupos as $g): ?>
		<?php
			$pct = $g['total'] > 0 ? (int) round($g['SOLD'] * 100 / $g['total']) : 0;
		?>
		<div class="raffle-panel">
			<div class="raffle-panel-header" data-bs-toggle="collapse" data-bs-target="#rt-<?= $g['raffle_id'] ?>" role="button">
				<div class="raffle-panel-title">
					<span class="raffle-icon"><i class="bx bx-ticket"></i></span>
					<div>
						<div class="fw-bold"><?= esc($g['raffle_name']) ?></div>
						<?php if (!empty($g['raffle_code'])): ?>
							<small class="text-muted"><?= esc($g['raffle_code']) ?></small>
						<?php endif; ?>
					</div>
					<span class="badge bg-light text-dark border"><?= (int) $g['total'] ?> boletas</span>
					<span class="badge bg-success-subtle text-success"><?= (int) $g['AVAILABLE'] ?> disponibles</span>
					<span class="badge bg-warning-subtle text-warning"><?= (int) $g['RESERVED'] ?> reservadas</span>
					<span class="badge bg-primary-subtle text-primary"><?= (int) $g['SOLD'] ?> vendidas</span>
					<?php if ((int) $g['CANCELLED'] > 0): ?>
						<span class="badge bg-secondary-subtle text-secondary"><?= (int) $g['CANCELLED'] ?> canceladas</span>
					<?php endif; ?>
				</div>
				<div class="d-flex align-items-center gap-3">
					<div class="d-flex align-items-center gap-2">
						<div class="raffle-progress">
							<div class="raffle-progress-bar" style="width: <?= $pct ?>%"></div>
						</div>
						<small class="text-muted"><?= $pct ?>%</small>
					</div>
					<i class="bx bx-chevron-down chev"></i>
				</div>
			</div>
			<div class="collapse show" id="rt-<?= $g['raffle_id'] ?>">
				<div class="raffle-panel-body">
					<div class="row g-3" id="grid-<?= $g['raffle_id'] ?>">
						<?php foreach ($g['tickets'] as $t): ?>
							<?= view('pages/tickets/_card', ['t' => $t]) ?>
						<?php endforeach; ?>
					</div>
					<div class="text-center mt-3" id="more-<?= $g['raffle_id'] ?>">
						<?php if ($g['restantes'] > 0): ?>
							<button type="button" class="btn btn-outline-primary btn-cargar-mas"
									data-raffle="<?= $g['raffle_id'] ?>">
								<i class="bx bx-download me-1"></i>Cargar más (<?= (int) $g['restantes'] ?> restantes)
							</button>
						<?php else: ?>
							<span class="text-muted small"><i class="bx bx-check-circle"></i> Todas las boletas cargadas</span>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
	function bindConfirm(root) {
		root.querySelectorAll('a[data-confirm]').forEach(function (el) {
			if (el.dataset.bound) return;
			el.dataset.bound = '1';
			el.addEventListener('click', function (e) {
				e.preventDefault();
				const url = el.getAttribute('href');
				Swal.fire({
					title: el.dataset.confirm || '¿Estás seguro?',
					text: el.dataset.confirmText || 'Esta acción no se puede deshacer',
					icon: el.dataset.confirmIcon || 'warning',
					showCancelButton: true,
					confirmButtonColor: '#dc3545',
					cancelButtonColor: '#6c757d',
					confirmButtonText: el.dataset.confirmBtn || 'Sí, continuar',
					cancelButtonText: el.dataset.cancelBtn || 'Cancelar'
				}).then(function (result) {
					if (result.isConfirmed) {
						window.location.href = url;
					}
				});
			});
		});
	}

	document.querySelectorAll('.btn-cargar-mas').forEach(function (btn) {
		btn.addEventListener('click', function () {
			const raffleId = btn.dataset.raffle;
			const grid = document.getElementById('grid-' + raffleId);
			const contenedor = document.getElementById('more-' + raffleId);
			if (!grid) return;

			const offset = grid.querySelectorAll('.ticket-card').length;
			const htmlOriginal = btn.innerHTML;

			btn.disabled = true;
			btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Cargando...';

			fetch('<?= base_url('boletas/cargar') ?>?raffle_id=' + encodeURIComponent(raffleId) + '&offset=' + offset)
				.then(function (res) {
					if (!res.ok) throw new Error('HTTP ' + res.status);
					return res.json();
				})
				.then(function (data) {
					const tmp = document.createElement('div');
					tmp.innerHTML = data.html || '';
					while (tmp.firstChild) {
						grid.appendChild(tmp.firstChild);
					}
					bindConfirm(grid);

					if (data.restantes > 0) {
						btn.disabled = false;
						btn.innerHTML = '<i class="bx bx-download me-1"></i>Cargar más (' + data.restantes + ' restantes)';
					} else {
						contenedor.innerHTML = '<span class="text-muted small"><i class="bx bx-check-circle"></i> Todas las boletas cargadas</span>';
					}
				})
				.catch(function () {
					btn.disabled = false;
					btn.innerHTML = htmlOriginal;
					showToast('No se pudieron cargar las boletas', 'error');
				});
		});
	});
});
</script>
<?= $this->endSection() ?>
