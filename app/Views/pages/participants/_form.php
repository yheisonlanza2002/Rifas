<?php
/**
 * Formulario compartido de participante (create/edit).
 * Variables: $action, $participant (null), $documentTypes, $errors, $isEdit
 */
$errors ??= null;
$participant ??= [];
$isEdit ??= false;
?>

<form method="post" action="<?= $action ?>" class="row g-3">
	<?= csrf_field() ?>

	<div class="col-md-4">
		<label for="document_type" class="form-label">Tipo de Documento <span class="text-danger">*</span></label>
		<select class="form-select <?= !empty($errors['document_type'] ?? null) ? 'is-invalid' : '' ?>"
				id="document_type" name="document_type">
			<option value="">Seleccionar...</option>
			<?php $docType = old('document_type') ?? ($participant['document_type'] ?? ''); ?>
			<?php foreach ($documentTypes as $key => $label): ?>
				<option value="<?= $key ?>" <?= $docType === $key ? 'selected' : '' ?>><?= esc($label) ?></option>
			<?php endforeach; ?>
		</select>
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'document_type']) ?>
	</div>
	<div class="col-md-4">
		<label for="document_number" class="form-label">Número de Documento <span class="text-danger">*</span></label>
		<input type="text" class="form-control <?= !empty($errors['document_number'] ?? null) ? 'is-invalid' : '' ?>"
			   id="document_number" name="document_number" placeholder="Ej: 1234567890"
			   value="<?= esc(old('document_number') ?? ($participant['document_number'] ?? '')) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'document_number']) ?>
	</div>
	<div class="col-md-4">
		<label for="phone" class="form-label">Teléfono <span class="text-danger">*</span></label>
		<input type="text" class="form-control <?= !empty($errors['phone'] ?? null) ? 'is-invalid' : '' ?>"
			   id="phone" name="phone" placeholder="3001234567"
			   value="<?= esc(old('phone') ?? ($participant['phone'] ?? '')) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'phone']) ?>
	</div>

	<div class="col-md-6">
		<label for="name" class="form-label">Nombre <span class="text-danger">*</span></label>
		<input type="text" class="form-control <?= !empty($errors['name'] ?? null) ? 'is-invalid' : '' ?>"
			   id="name" name="name" placeholder="Ej: Juan"
			   value="<?= esc(old('name') ?? ($participant['name'] ?? '')) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'name']) ?>
	</div>
	<div class="col-md-6">
		<label for="last_name" class="form-label">Apellido</label>
		<input type="text" class="form-control <?= !empty($errors['last_name'] ?? null) ? 'is-invalid' : '' ?>"
			   id="last_name" name="last_name" placeholder="Ej: Pérez"
			   value="<?= esc(old('last_name') ?? ($participant['last_name'] ?? '')) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'last_name']) ?>
	</div>

	<div class="col-md-6">
		<label for="email" class="form-label">Email</label>
		<input type="email" class="form-control <?= !empty($errors['email'] ?? null) ? 'is-invalid' : '' ?>"
			   id="email" name="email" placeholder="participante@correo.com"
			   value="<?= esc(old('email') ?? ($participant['email'] ?? '')) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'email']) ?>
	</div>
	<div class="col-md-6">
		<label for="birth_date" class="form-label">Fecha de Nacimiento</label>
		<input type="date" class="form-control <?= !empty($errors['birth_date'] ?? null) ? 'is-invalid' : '' ?>"
			   id="birth_date" name="birth_date"
			   value="<?= esc(old('birth_date') ?? ($participant['birth_date'] ?? '')) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'birth_date']) ?>
	</div>

	<div class="col-md-4">
		<label for="gender" class="form-label">Género</label>
		<?php $gender = old('gender') ?? ($participant['gender'] ?? ''); ?>
		<select class="form-select <?= !empty($errors['gender'] ?? null) ? 'is-invalid' : '' ?>"
				id="gender" name="gender">
			<option value="">No especificado</option>
			<option value="M" <?= $gender === 'M' ? 'selected' : '' ?>>Masculino</option>
			<option value="F" <?= $gender === 'F' ? 'selected' : '' ?>>Femenino</option>
			<option value="O" <?= $gender === 'O' ? 'selected' : '' ?>>Otro</option>
		</select>
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'gender']) ?>
	</div>
	<div class="col-md-4">
		<label for="city" class="form-label">Ciudad</label>
		<input type="text" class="form-control <?= !empty($errors['city'] ?? null) ? 'is-invalid' : '' ?>"
			   id="city" name="city" placeholder="Bogotá"
			   value="<?= esc(old('city') ?? ($participant['city'] ?? '')) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'city']) ?>
	</div>
	<div class="col-md-4">
		<label for="department" class="form-label">Departamento</label>
		<input type="text" class="form-control <?= !empty($errors['department'] ?? null) ? 'is-invalid' : '' ?>"
			   id="department" name="department" placeholder="Cundinamarca"
			   value="<?= esc(old('department') ?? ($participant['department'] ?? '')) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'department']) ?>
	</div>

	<div class="col-12">
		<label for="address" class="form-label">Dirección</label>
		<input type="text" class="form-control <?= !empty($errors['address'] ?? null) ? 'is-invalid' : '' ?>"
			   id="address" name="address" placeholder="Calle 100 # 10-20"
			   value="<?= esc(old('address') ?? ($participant['address'] ?? '')) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'address']) ?>
	</div>

	<div class="col-12">
		<label for="notes" class="form-label">Notas</label>
		<textarea class="form-control <?= !empty($errors['notes'] ?? null) ? 'is-invalid' : '' ?>"
				  id="notes" name="notes" rows="2" placeholder="Notas adicionales del participante..."><?= esc(old('notes') ?? ($participant['notes'] ?? '')) ?></textarea>
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'notes']) ?>
	</div>

	<div class="col-12">
		<hr>
		<div class="d-flex gap-2">
			<button type="submit" class="btn btn-primary px-4"><i class="bx bx-save"></i> <?= $isEdit ? 'Actualizar' : 'Guardar' ?></button>
			<a href="<?= base_url('participantes') ?>" class="btn btn-outline-secondary px-4"><i class="bx bx-arrow-back"></i> Volver</a>
		</div>
	</div>
</form>
