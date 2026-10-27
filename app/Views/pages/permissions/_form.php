<form action="<?= isset($permission) ? base_url('permisos/' . $permission['id']) : base_url('permisos') ?>"
	  method="POST" class="row g-3">
	<?= csrf_field() ?>
	<?php if (isset($permission)): ?>
		<input type="hidden" name="_method" value="PUT">
	<?php endif; ?>

	<div class="col-md-6">
		<label for="name" class="form-label">Nombre <span class="text-danger">*</span></label>
		<input type="text" name="name" id="name"
			   class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
			   value="<?= isset($permission) ? esc($permission['name']) : old('name') ?>"
			   placeholder="Ej: Crear Usuario" required>
		<?php if (isset($errors['name'])): ?>
			<div class="invalid-feedback"><?= $errors['name'] ?></div>
		<?php endif; ?>
	</div>

	<div class="col-md-6">
		<label for="module" class="form-label">Módulo <span class="text-danger">*</span></label>
		<input type="text" name="module" id="module" list="modules-list"
			   class="form-control <?= isset($errors['module']) ? 'is-invalid' : '' ?>"
			   value="<?= isset($permission) ? esc($permission['module']) : old('module') ?>"
			   placeholder="Ej: users" required>
		<datalist id="modules-list">
			<?php foreach ($modules as $m): ?>
				<option value="<?= esc($m) ?>">
			<?php endforeach; ?>
		</datalist>
		<?php if (isset($errors['module'])): ?>
			<div class="invalid-feedback"><?= $errors['module'] ?></div>
		<?php endif; ?>
	</div>

	<div class="col-md-6">
		<label for="action" class="form-label">Acción <span class="text-danger">*</span></label>
		<input type="text" name="action" id="action"
			   class="form-control <?= isset($errors['action']) ? 'is-invalid' : '' ?>"
			   value="<?= isset($permission) ? esc($permission['action']) : old('action') ?>"
			   placeholder="Ej: create" required>
		<?php if (isset($errors['action'])): ?>
			<div class="invalid-feedback"><?= $errors['action'] ?></div>
		<?php endif; ?>
	</div>

	<div class="col-md-6">
		<label for="guard_name" class="form-label">Guard <span class="text-danger">*</span></label>
		<select name="guard_name" id="guard_name" class="form-select <?= isset($errors['guard_name']) ? 'is-invalid' : '' ?>" required>
			<?php foreach ($guards as $key => $label): ?>
				<option value="<?= $key ?>"
					<?= (isset($permission) ? $permission['guard_name'] : old('guard_name', 'web')) == $key ? 'selected' : '' ?>>
					<?= $label ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php if (isset($errors['guard_name'])): ?>
			<div class="invalid-feedback"><?= $errors['guard_name'] ?></div>
		<?php endif; ?>
	</div>

	<div class="col-md-12">
		<label for="description" class="form-label">Descripción</label>
		<textarea name="description" id="description" rows="2"
				  class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>"
				  placeholder="Descripción del permiso..."><?= isset($permission) ? esc($permission['description']) : old('description') ?></textarea>
		<?php if (isset($errors['description'])): ?>
			<div class="invalid-feedback"><?= $errors['description'] ?></div>
		<?php endif; ?>
	</div>

	<div class="col-md-6">
		<label for="status" class="form-label">Estado <span class="text-danger">*</span></label>
		<select name="status" id="status" class="form-select <?= isset($errors['status']) ? 'is-invalid' : '' ?>" required>
			<?php foreach ($statuses as $key => $label): ?>
				<option value="<?= $key ?>"
					<?= (isset($permission) ? $permission['status'] : old('status')) == $key ? 'selected' : '' ?>>
					<?= $label ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php if (isset($errors['status'])): ?>
			<div class="invalid-feedback"><?= $errors['status'] ?></div>
		<?php endif; ?>
	</div>

	<div class="col-12">
		<button type="submit" class="btn btn-primary px-4">
			<i class="bx bx-save"></i> <?= isset($permission) ? 'Actualizar' : 'Guardar' ?>
		</button>
		<a href="<?= base_url('permisos') ?>" class="btn btn-outline-secondary px-4">Cancelar</a>
	</div>
</form>
