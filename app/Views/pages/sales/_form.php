<?php
$errors ??= null;
$sale ??= [];
$isEdit ??= false;
?>

<form method="post" action="<?= $action ?>" class="row g-3">
	<?= csrf_field() ?>

	<div class="col-md-6">
		<label for="participant_id" class="form-label">Participante <span class="text-danger">*</span></label>
		<?php $participantId = old('participant_id') ?? ($sale['participant_id'] ?? ''); ?>
		<select class="form-select <?= !empty($errors['participant_id'] ?? null) ? 'is-invalid' : '' ?>"
				id="participant_id" name="participant_id">
			<option value="">Seleccionar participante...</option>
			<?php foreach ($participants as $p): ?>
				<option value="<?= esc($p['id']) ?>" <?= $participantId == $p['id'] ? 'selected' : '' ?>>
					<?= esc($p['name'] . ' ' . ($p['last_name'] ?? '')) ?> (<?= esc($p['document_number']) ?>)
				</option>
			<?php endforeach; ?>
		</select>
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'participant_id']) ?>
	</div>
	<div class="col-md-6">
		<label for="sale_code" class="form-label">Código de Venta <span class="text-danger">*</span></label>
		<input type="text" class="form-control <?= !empty($errors['sale_code'] ?? null) ? 'is-invalid' : '' ?>"
			   id="sale_code" name="sale_code" placeholder="Ej: VENTA-2026-001"
			   value="<?= esc(old('sale_code') ?? ($sale['sale_code'] ?? '')) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'sale_code']) ?>
	</div>

	<div class="col-md-4">
		<label for="status" class="form-label">Estado <span class="text-danger">*</span></label>
		<?php $status = old('status') ?? ($sale['status'] ?? 'PENDING'); ?>
		<select class="form-select <?= !empty($errors['status'] ?? null) ? 'is-invalid' : '' ?>"
				id="status" name="status">
			<?php foreach ($statuses as $key => $label): ?>
				<option value="<?= $key ?>" <?= $status === $key ? 'selected' : '' ?>><?= esc($label) ?></option>
			<?php endforeach; ?>
		</select>
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'status']) ?>
	</div>
	<div class="col-md-4">
		<label for="quantity" class="form-label">Cantidad <span class="text-danger">*</span></label>
		<input type="number" class="form-control <?= !empty($errors['quantity'] ?? null) ? 'is-invalid' : '' ?>"
			   id="quantity" name="quantity" placeholder="Ej: 1" min="1"
			   value="<?= esc(old('quantity') ?? ($sale['quantity'] ?? 1)) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'quantity']) ?>
	</div>
	<div class="col-md-4">
		<label for="currency" class="form-label">Moneda <span class="text-danger">*</span></label>
		<?php $currency = old('currency') ?? ($sale['currency'] ?? 'COP'); ?>
		<select class="form-select <?= !empty($errors['currency'] ?? null) ? 'is-invalid' : '' ?>"
				id="currency" name="currency">
			<?php foreach ($currencies as $key => $label): ?>
				<option value="<?= $key ?>" <?= $currency === $key ? 'selected' : '' ?>><?= esc($label) ?></option>
			<?php endforeach; ?>
		</select>
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'currency']) ?>
	</div>

	<div class="col-md-4">
		<label for="subtotal" class="form-label">Subtotal <span class="text-danger">*</span></label>
		<input type="number" step="0.01" class="form-control <?= !empty($errors['subtotal'] ?? null) ? 'is-invalid' : '' ?>"
			   id="subtotal" name="subtotal" placeholder="Ej: 50000.00" min="0"
			   value="<?= esc(old('subtotal') ?? ($sale['subtotal'] ?? '')) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'subtotal']) ?>
	</div>
	<div class="col-md-4">
		<label for="discount" class="form-label">Descuento</label>
		<input type="number" step="0.01" class="form-control <?= !empty($errors['discount'] ?? null) ? 'is-invalid' : '' ?>"
			   id="discount" name="discount" placeholder="Ej: 0.00" min="0"
			   value="<?= esc(old('discount') ?? ($sale['discount'] ?? 0)) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'discount']) ?>
	</div>
	<div class="col-md-4">
		<label for="tax_amount" class="form-label">Impuesto</label>
		<input type="number" step="0.01" class="form-control <?= !empty($errors['tax_amount'] ?? null) ? 'is-invalid' : '' ?>"
			   id="tax_amount" name="tax_amount" placeholder="Ej: 0.00" min="0"
			   value="<?= esc(old('tax_amount') ?? ($sale['tax_amount'] ?? 0)) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'tax_amount']) ?>
	</div>

	<div class="col-md-4">
		<label for="commission_amount" class="form-label">Comisión</label>
		<input type="number" step="0.01" class="form-control <?= !empty($errors['commission_amount'] ?? null) ? 'is-invalid' : '' ?>"
			   id="commission_amount" name="commission_amount" placeholder="Ej: 0.00" min="0"
			   value="<?= esc(old('commission_amount') ?? ($sale['commission_amount'] ?? 0)) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'commission_amount']) ?>
	</div>
	<div class="col-md-4">
		<label for="total" class="form-label">Total <span class="text-danger">*</span></label>
		<input type="number" step="0.01" class="form-control <?= !empty($errors['total'] ?? null) ? 'is-invalid' : '' ?>"
			   id="total" name="total" placeholder="Ej: 50000.00" min="0.01"
			   value="<?= esc(old('total') ?? ($sale['total'] ?? '')) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'total']) ?>
	</div>
	<div class="col-md-4">
		<label for="paid_amount" class="form-label">Monto Pagado</label>
		<input type="number" step="0.01" class="form-control <?= !empty($errors['paid_amount'] ?? null) ? 'is-invalid' : '' ?>"
			   id="paid_amount" name="paid_amount" placeholder="Ej: 0.00" min="0"
			   value="<?= esc(old('paid_amount') ?? ($sale['paid_amount'] ?? 0)) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'paid_amount']) ?>
	</div>

	<div class="col-12">
		<label for="notes" class="form-label">Notas</label>
		<textarea class="form-control <?= !empty($errors['notes'] ?? null) ? 'is-invalid' : '' ?>"
				  id="notes" name="notes" rows="2" placeholder="Notas adicionales de la venta..."><?= esc(old('notes') ?? ($sale['notes'] ?? '')) ?></textarea>
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'notes']) ?>
	</div>

	<div class="col-12">
		<hr>
		<div class="d-flex gap-2">
			<button type="submit" class="btn btn-primary px-4"><i class="bx bx-save"></i> <?= $isEdit ? 'Actualizar' : 'Guardar' ?></button>
			<a href="<?= base_url('ventas') ?>" class="btn btn-outline-secondary px-4"><i class="bx bx-arrow-back"></i> Volver</a>
		</div>
	</div>
</form>
