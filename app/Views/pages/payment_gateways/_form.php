<?php
$errors ??= null;
$gateway ??= [];
$isEdit ??= false;
?>

<form method="post" action="<?= $action ?>" class="row g-3">
	<?= csrf_field() ?>

	<div class="col-md-6">
		<label for="name" class="form-label">Nombre <span class="text-danger">*</span></label>
		<input type="text" class="form-control <?= !empty($errors['name'] ?? null) ? 'is-invalid' : '' ?>"
			   id="name" name="name" placeholder="Ej: Stripe"
			   value="<?= esc(old('name') ?? ($gateway['name'] ?? '')) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'name']) ?>
	</div>
	<div class="col-md-6">
		<label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
		<input type="text" class="form-control <?= !empty($errors['slug'] ?? null) ? 'is-invalid' : '' ?>"
			   id="slug" name="slug" placeholder="Ej: stripe"
			   value="<?= esc(old('slug') ?? ($gateway['slug'] ?? '')) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'slug']) ?>
	</div>

	<div class="col-md-6">
		<label for="driver" class="form-label">Driver <span class="text-danger">*</span></label>
		<input type="text" class="form-control <?= !empty($errors['driver'] ?? null) ? 'is-invalid' : '' ?>"
			   id="driver" name="driver" placeholder="Ej: StripeDriver"
			   value="<?= esc(old('driver') ?? ($gateway['driver'] ?? '')) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'driver']) ?>
	</div>
	<div class="col-md-6">
		<label for="sort_order" class="form-label">Orden</label>
		<input type="number" class="form-control <?= !empty($errors['sort_order'] ?? null) ? 'is-invalid' : '' ?>"
			   id="sort_order" name="sort_order" placeholder="Ej: 0" min="0"
			   value="<?= esc(old('sort_order') ?? ($gateway['sort_order'] ?? 0)) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'sort_order']) ?>
	</div>

	<div class="col-md-6">
		<label for="test_mode" class="form-label">Modo de Prueba <span class="text-danger">*</span></label>
		<?php $testMode = old('test_mode') ?? ($gateway['test_mode'] ?? 1); ?>
		<select class="form-select <?= !empty($errors['test_mode'] ?? null) ? 'is-invalid' : '' ?>"
				id="test_mode" name="test_mode">
			<option value="1" <?= $testMode == 1 ? 'selected' : '' ?>>Sí (Prueba)</option>
			<option value="0" <?= $testMode == 0 ? 'selected' : '' ?>>No (Producción)</option>
		</select>
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'test_mode']) ?>
	</div>
	<div class="col-md-6">
		<label for="status" class="form-label">Estado <span class="text-danger">*</span></label>
		<?php $status = old('status') ?? ($gateway['status'] ?? 'ACTIVE'); ?>
		<select class="form-select <?= !empty($errors['status'] ?? null) ? 'is-invalid' : '' ?>"
				id="status" name="status">
			<?php foreach ($statuses as $key => $label): ?>
				<option value="<?= $key ?>" <?= $status === $key ? 'selected' : '' ?>><?= esc($label) ?></option>
			<?php endforeach; ?>
		</select>
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'status']) ?>
	</div>

	<div class="col-12">
		<hr>
		<div class="d-flex gap-2">
			<button type="submit" class="btn btn-primary px-4"><i class="bx bx-save"></i> <?= $isEdit ? 'Actualizar' : 'Guardar' ?></button>
			<a href="<?= base_url('pasarelas') ?>" class="btn btn-outline-secondary px-4"><i class="bx bx-arrow-back"></i> Volver</a>
		</div>
	</div>
</form>
