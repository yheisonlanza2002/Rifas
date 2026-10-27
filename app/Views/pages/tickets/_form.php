<?php
$errors ??= null;
$ticket ??= [];
$isEdit ??= false;
?>

<form method="post" action="<?= $action ?>" class="row g-3">
	<?= csrf_field() ?>

	<div class="col-md-6">
		<label for="raffle_id" class="form-label">Rifa <span class="text-danger">*</span></label>
		<?php $raffleId = old('raffle_id') ?? ($ticket['raffle_id'] ?? ''); ?>
		<select class="form-select <?= !empty($errors['raffle_id'] ?? null) ? 'is-invalid' : '' ?>"
				id="raffle_id" name="raffle_id">
			<option value="">Seleccionar rifa...</option>
			<?php foreach ($raffles as $r): ?>
				<option value="<?= esc($r['id']) ?>" <?= $raffleId == $r['id'] ? 'selected' : '' ?>>
					<?= esc($r['name']) ?> (<?= esc($r['code']) ?>)
				</option>
			<?php endforeach; ?>
		</select>
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'raffle_id']) ?>
	</div>
	<div class="col-md-6">
		<label for="code" class="form-label">Código <span class="text-danger">*</span></label>
		<input type="text" class="form-control <?= !empty($errors['code'] ?? null) ? 'is-invalid' : '' ?>"
			   id="code" name="code" placeholder="Ej: TICKET-001"
			   value="<?= esc(old('code') ?? ($ticket['code'] ?? '')) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'code']) ?>
	</div>

	<div class="col-md-4">
		<label for="serial_prefix" class="form-label">Prefijo Serial</label>
		<input type="text" class="form-control <?= !empty($errors['serial_prefix'] ?? null) ? 'is-invalid' : '' ?>"
			   id="serial_prefix" name="serial_prefix" placeholder="Ej: TKT"
			   value="<?= esc(old('serial_prefix') ?? ($ticket['serial_prefix'] ?? '')) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'serial_prefix']) ?>
	</div>
	<div class="col-md-4">
		<label for="price" class="form-label">Precio <span class="text-danger">*</span></label>
		<input type="number" step="0.01" class="form-control <?= !empty($errors['price'] ?? null) ? 'is-invalid' : '' ?>"
			   id="price" name="price" placeholder="Ej: 5000.00" min="0.01"
			   value="<?= esc(old('price') ?? ($ticket['price'] ?? '')) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'price']) ?>
	</div>
	<div class="col-md-4">
		<label for="status" class="form-label">Estado <span class="text-danger">*</span></label>
		<?php $status = old('status') ?? ($ticket['status'] ?? 'AVAILABLE'); ?>
		<select class="form-select <?= !empty($errors['status'] ?? null) ? 'is-invalid' : '' ?>"
				id="status" name="status">
			<?php foreach ($statuses as $key => $label): ?>
				<option value="<?= $key ?>" <?= $status === $key ? 'selected' : '' ?>><?= esc($label) ?></option>
			<?php endforeach; ?>
		</select>
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'status']) ?>
	</div>

	<div class="col-md-6">
		<label for="number_quantity" class="form-label">Cantidad de Números <span class="text-danger">*</span></label>
		<input type="number" class="form-control <?= !empty($errors['number_quantity'] ?? null) ? 'is-invalid' : '' ?>"
			   id="number_quantity" name="number_quantity" placeholder="Ej: 1" min="1"
			   value="<?= esc(old('number_quantity') ?? ($ticket['number_quantity'] ?? 1)) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'number_quantity']) ?>
	</div>
	<div class="col-md-6">
		<label for="bundle_quantity" class="form-label">Cantidad del Paquete</label>
		<input type="number" class="form-control <?= !empty($errors['bundle_quantity'] ?? null) ? 'is-invalid' : '' ?>"
			   id="bundle_quantity" name="bundle_quantity" placeholder="Ej: 1" min="1"
			   value="<?= esc(old('bundle_quantity') ?? ($ticket['bundle_quantity'] ?? 1)) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'bundle_quantity']) ?>
	</div>

	<div class="col-12">
		<hr>
		<div class="d-flex gap-2">
			<button type="submit" class="btn btn-primary px-4"><i class="bx bx-save"></i> <?= $isEdit ? 'Actualizar' : 'Guardar' ?></button>
			<a href="<?= base_url('boletas') ?>" class="btn btn-outline-secondary px-4"><i class="bx bx-arrow-back"></i> Volver</a>
		</div>
	</div>
</form>
