<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
	<div class="breadcrumb-title pe-3">Administración</div>
	<div class="ps-3">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb mb-0 p-0">
				<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="bx bx-home-alt"></i></a></li>
				<li class="breadcrumb-item"><a href="<?= base_url('ventas') ?>">Ventas</a></li>
				<li class="breadcrumb-item active" aria-current="page">Registrar Pago</li>
			</ol>
		</nav>
	</div>
</div>

<div class="row g-3 justify-content-center">
	<div class="col-lg-7">
		<div class="card">
			<div class="card-header">
				<h6 class="mb-0 fw-bold"><i class="bx bx-money me-1"></i> Registrar pago — <?= esc($sale['sale_code']) ?></h6>
			</div>
			<div class="card-body">
				<div class="row g-3 mb-4">
					<div class="col-sm-6">
						<small class="text-muted text-uppercase">Participante</small>
						<div class="fw-semibold">
							<?= esc(trim(($participant['name'] ?? '') . ' ' . ($participant['last_name'] ?? ''))) ?: '—' ?>
						</div>
					</div>
					<div class="col-sm-3">
						<small class="text-muted text-uppercase">Total</small>
						<div class="fw-semibold">$ <?= number_format((float) $sale['total'], 2) ?></div>
					</div>
					<div class="col-sm-3">
						<small class="text-muted text-uppercase">Pendiente</small>
						<div class="fw-bold text-warning">$ <?= number_format((float) $sale['pending_amount'], 2) ?></div>
					</div>
				</div>

				<div class="progress mb-4" style="height: 10px;">
					<?php
						$total  = max(0.001, (float) $sale['total']);
						$pagado = (float) $sale['paid_amount'];
						$pct    = min(100, round($pagado * 100 / $total));
					?>
					<div class="progress-bar bg-success" style="width: <?= $pct ?>%"><?= $pct ?>%</div>
				</div>

				<form method="post" action="<?= base_url('ventas/' . $sale['id'] . '/registrar-pago') ?>" class="row g-3">
					<?= csrf_field() ?>

					<div class="col-md-6">
						<label for="amount" class="form-label">Monto a pagar <span class="text-danger">*</span></label>
						<input type="number" step="0.01" min="0.01" max="<?= esc(number_format((float) $sale['pending_amount'], 2, '.', '')) ?>"
							   class="form-control" id="amount" name="amount" required
							   value="<?= esc(old('amount') ?? number_format((float) $sale['pending_amount'], 2, '.', '')) ?>">
						<div class="form-text">Saldo pendiente: $ <?= number_format((float) $sale['pending_amount'], 2) ?></div>
					</div>
					<div class="col-md-6">
						<label for="payment_method" class="form-label">Método <span class="text-danger">*</span></label>
						<select class="form-select" id="payment_method" name="payment_method" required>
							<?php foreach ($methods as $key => $label): ?>
								<option value="<?= esc($key) ?>" <?= old('payment_method', 'CASH') === $key ? 'selected' : '' ?>>
									<?= esc($label) ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="col-12" id="refGroup">
						<label for="reference" class="form-label">Referencia de transferencia</label>
						<input type="text" class="form-control" id="reference" name="reference" maxlength="150"
							   placeholder="Ej: TRX-987654" value="<?= esc(old('reference') ?? '') ?>">
					</div>

					<?php $errores = session()->get('errors'); ?>
					<?php if (!empty($errores)): ?>
						<div class="col-12">
							<div class="alert alert-danger py-2 mb-0">
								<ul class="mb-0 ps-3">
									<?php foreach ($errores as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?>
								</ul>
							</div>
						</div>
					<?php endif; ?>

					<div class="col-12">
						<hr>
						<div class="d-flex gap-2">
							<button type="submit" class="btn btn-success px-4"><i class="bx bx-check"></i> Registrar pago</button>
							<a href="<?= base_url('ventas') ?>" class="btn btn-outline-secondary px-4"><i class="bx bx-arrow-back"></i> Volver</a>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
	const metodo = document.getElementById('payment_method');
	const grupo = document.getElementById('refGroup');
	const toggle = () => grupo.classList.toggle('d-none', metodo.value !== 'TRANSFER');
	metodo.addEventListener('change', toggle);
	toggle();
});
</script>
<?= $this->endSection() ?>
