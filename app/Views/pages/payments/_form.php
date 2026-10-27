<?php
$errors ??= null;
$payment ??= [];
$isEdit ??= false;
?>

<form method="post" action="<?= $action ?>" class="row g-3">
	<?= csrf_field() ?>

	<div class="col-md-6">
		<label for="sale_id" class="form-label">Venta <span class="text-danger">*</span></label>
		<?php $saleId = old('sale_id') ?? ($payment['sale_id'] ?? ''); ?>
		<select class="form-select <?= !empty($errors['sale_id'] ?? null) ? 'is-invalid' : '' ?>"
				id="sale_id" name="sale_id">
			<option value="">Seleccionar venta...</option>
			<?php foreach ($sales as $s): ?>
				<option value="<?= esc($s['id']) ?>" <?= $saleId == $s['id'] ? 'selected' : '' ?>>
					<?= esc($s['sale_code']) ?> - $ <?= number_format($s['total'], 2) ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'sale_id']) ?>
	</div>
	<div class="col-md-6">
		<label for="gateway_id" class="form-label">Pasarela</label>
		<?php $gatewayId = old('gateway_id') ?? ($payment['gateway_id'] ?? ''); ?>
		<select class="form-select <?= !empty($errors['gateway_id'] ?? null) ? 'is-invalid' : '' ?>"
				id="gateway_id" name="gateway_id">
			<option value="">Seleccionar pasarela...</option>
			<?php foreach ($gateways as $g): ?>
				<option value="<?= esc($g['id']) ?>" <?= $gatewayId == $g['id'] ? 'selected' : '' ?>>
					<?= esc($g['name']) ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'gateway_id']) ?>
	</div>

	<div class="col-md-4">
		<label for="amount" class="form-label">Monto <span class="text-danger">*</span></label>
		<input type="number" step="0.01" class="form-control <?= !empty($errors['amount'] ?? null) ? 'is-invalid' : '' ?>"
			   id="amount" name="amount" placeholder="Ej: 50000.00" min="0.01"
			   value="<?= esc(old('amount') ?? ($payment['amount'] ?? '')) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'amount']) ?>
	</div>
	<div class="col-md-4">
		<label for="fee_amount" class="form-label">Comisión</label>
		<input type="number" step="0.01" class="form-control <?= !empty($errors['fee_amount'] ?? null) ? 'is-invalid' : '' ?>"
			   id="fee_amount" name="fee_amount" placeholder="Ej: 0.00" min="0"
			   value="<?= esc(old('fee_amount') ?? ($payment['fee_amount'] ?? 0)) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'fee_amount']) ?>
	</div>
	<div class="col-md-4">
		<label for="net_amount" class="form-label">Monto Neto</label>
		<input type="number" step="0.01" class="form-control <?= !empty($errors['net_amount'] ?? null) ? 'is-invalid' : '' ?>"
			   id="net_amount" name="net_amount" placeholder="Ej: 50000.00" min="0"
			   value="<?= esc(old('net_amount') ?? ($payment['net_amount'] ?? '')) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'net_amount']) ?>
	</div>

	<div class="col-md-6">
		<label for="payment_method" class="form-label">Método de Pago <span class="text-danger">*</span></label>
		<input type="text" class="form-control <?= !empty($errors['payment_method'] ?? null) ? 'is-invalid' : '' ?>"
			   id="payment_method" name="payment_method" placeholder="Ej: Tarjeta de Crédito"
			   value="<?= esc(old('payment_method') ?? ($payment['payment_method'] ?? '')) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'payment_method']) ?>
	</div>
	<div class="col-md-6">
		<label for="status" class="form-label">Estado <span class="text-danger">*</span></label>
		<?php $status = old('status') ?? ($payment['status'] ?? 'PENDING'); ?>
		<select class="form-select <?= !empty($errors['status'] ?? null) ? 'is-invalid' : '' ?>"
				id="status" name="status">
			<?php foreach ($statuses as $key => $label): ?>
				<option value="<?= $key ?>" <?= $status === $key ? 'selected' : '' ?>><?= esc($label) ?></option>
			<?php endforeach; ?>
		</select>
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'status']) ?>
	</div>

	<div class="col-md-6">
		<label for="transaction_reference" class="form-label">Referencia de Transacción</label>
		<input type="text" class="form-control <?= !empty($errors['transaction_reference'] ?? null) ? 'is-invalid' : '' ?>"
			   id="transaction_reference" name="transaction_reference" placeholder="Ej: TXN-123456789"
			   value="<?= esc(old('transaction_reference') ?? ($payment['transaction_reference'] ?? '')) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'transaction_reference']) ?>
	</div>
	<div class="col-md-6">
		<label for="gateway" class="form-label">Pasarela (Texto)</label>
		<input type="text" class="form-control <?= !empty($errors['gateway'] ?? null) ? 'is-invalid' : '' ?>"
			   id="gateway" name="gateway" placeholder="Ej: Stripe"
			   value="<?= esc(old('gateway') ?? ($payment['gateway'] ?? '')) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'gateway']) ?>
	</div>

	<div class="col-12">
		<hr>
		<div class="d-flex gap-2">
			<button type="submit" class="btn btn-primary px-4"><i class="bx bx-save"></i> <?= $isEdit ? 'Actualizar' : 'Guardar' ?></button>
			<a href="<?= base_url('pagos') ?>" class="btn btn-outline-secondary px-4"><i class="bx bx-arrow-back"></i> Volver</a>
		</div>
	</div>
</form>
