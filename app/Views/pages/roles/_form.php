<form action="<?= isset($role) ? base_url('roles/' . $role['id']) : base_url('roles') ?>"
	  method="POST" class="row g-3">
	<?= csrf_field() ?>
	<?php if (isset($role)): ?>
		<input type="hidden" name="_method" value="PUT">
	<?php endif; ?>

	<div class="col-md-6">
		<label for="name" class="form-label">Nombre <span class="text-danger">*</span></label>
		<input type="text" name="name" id="name"
			   class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
			   value="<?= isset($role) ? esc($role['name']) : old('name') ?>"
			   placeholder="Ej: Administrador" required>
		<?php if (isset($errors['name'])): ?>
			<div class="invalid-feedback"><?= $errors['name'] ?></div>
		<?php endif; ?>
	</div>

	<div class="col-md-6">
		<label for="scope" class="form-label">Alcance <span class="text-danger">*</span></label>
		<select name="scope" id="scope" class="form-select <?= isset($errors['scope']) ? 'is-invalid' : '' ?>" required>
			<?php foreach ($scopes as $key => $label): ?>
				<option value="<?= $key ?>"
					<?= (isset($role) ? $role['scope'] : old('scope')) == $key ? 'selected' : '' ?>>
					<?= $label ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php if (isset($errors['scope'])): ?>
			<div class="invalid-feedback"><?= $errors['scope'] ?></div>
		<?php endif; ?>
	</div>

	<div class="col-md-12">
		<label for="description" class="form-label">Descripción</label>
		<textarea name="description" id="description" rows="2"
				  class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>"
				  placeholder="Descripción del rol..."><?= isset($role) ? esc($role['description']) : old('description') ?></textarea>
		<?php if (isset($errors['description'])): ?>
			<div class="invalid-feedback"><?= $errors['description'] ?></div>
		<?php endif; ?>
	</div>

	<div class="col-md-12">
		<label class="form-label">Permisos</label>
		<div class="card">
			<div class="card-body">
				<?php
				$currentModule = '';
				foreach ($permissions as $p):
					if ($currentModule !== $p['module']):
						$currentModule = $p['module'];
				?>
						<div class="mt-3">
							<strong class="text-primary"><?= esc($currentModule) ?></strong>
						</div>
				<?php
					endif;
				$checked = isset($rolePermissionIds) && in_array($p['id'], $rolePermissionIds) ? 'checked' : '';
				?>
					<div class="form-check">
						<input class="form-check-input" type="checkbox" name="permissions[]"
							   value="<?= $p['id'] ?>" id="perm_<?= $p['id'] ?>" <?= $checked ?>>
						<label class="form-check-label" for="perm_<?= $p['id'] ?>">
							<?= esc($p['name']) ?>
							<small class="text-muted">(<?= esc($p['action']) ?>)</small>
						</label>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<label for="status" class="form-label">Estado <span class="text-danger">*</span></label>
		<select name="status" id="status" class="form-select <?= isset($errors['status']) ? 'is-invalid' : '' ?>" required>
			<?php foreach ($statuses as $key => $label): ?>
				<option value="<?= $key ?>"
					<?= (isset($role) ? $role['status'] : old('status')) == $key ? 'selected' : '' ?>>
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
			<i class="bx bx-save"></i> <?= isset($role) ? 'Actualizar' : 'Guardar' ?>
		</button>
		<a href="<?= base_url('roles') ?>" class="btn btn-outline-secondary px-4">Cancelar</a>
	</div>
</form>
