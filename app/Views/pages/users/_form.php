<?php
/**
 * Formulario compartido de usuario (usado en create y edit).
 * Variables esperadas:
 *   $action    - URL del formulario
 *   $user      - datos del usuario (null en crear)
 *   $roles     - lista de roles activos
 *   $userRoles - ids de roles asignados (vacío en crear)
 *   $errors    - errores de validación (null si no hay)
 *   $isEdit    - bool
 */
$errors ??= null;
$user ??= [];
$userRoles ??= [];
$currentRoles = old('roles') ?? $userRoles;
?>

<form method="post" action="<?= $action ?>" class="row g-3">
	<?= csrf_field() ?>

	<div class="col-md-6">
		<label for="name" class="form-label">Nombre <span class="text-danger">*</span></label>
		<input type="text" class="form-control <?= !empty($errors['name'] ?? null) ? 'is-invalid' : '' ?>"
			   id="name" name="name" placeholder="Ej: Juan"
			   value="<?= esc(old('name') ?? ($user['name'] ?? '')) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'name']) ?>
	</div>
	<div class="col-md-6">
		<label for="last_name" class="form-label">Apellido</label>
		<input type="text" class="form-control <?= !empty($errors['last_name'] ?? null) ? 'is-invalid' : '' ?>"
			   id="last_name" name="last_name" placeholder="Ej: Pérez"
			   value="<?= esc(old('last_name') ?? ($user['last_name'] ?? '')) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'last_name']) ?>
	</div>
	<div class="col-md-6">
		<label for="email" class="form-label">Email <span class="text-danger">*</span></label>
		<input type="email" class="form-control <?= !empty($errors['email'] ?? null) ? 'is-invalid' : '' ?>"
			   id="email" name="email" placeholder="usuario@correo.com"
			   value="<?= esc(old('email') ?? ($user['email'] ?? '')) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'email']) ?>
	</div>
	<div class="col-md-6">
		<label for="phone" class="form-label">Teléfono</label>
		<input type="text" class="form-control <?= !empty($errors['phone'] ?? null) ? 'is-invalid' : '' ?>"
			   id="phone" name="phone" placeholder="3001234567"
			   value="<?= esc(old('phone') ?? ($user['phone'] ?? '')) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'phone']) ?>
	</div>

	<div class="col-md-6">
		<label for="password" class="form-label">
			Contraseña <?= $isEdit ? '<small class="text-muted">(dejar vacía para no cambiar)</small>' : '<span class="text-danger">*</span>' ?>
		</label>
		<div class="input-group" id="show_hide_password">
			<input type="password" class="form-control border-end-0 <?= !empty($errors['password'] ?? null) ? 'is-invalid' : '' ?>"
				   id="password" name="password" placeholder="<?= $isEdit ? 'Nueva contraseña' : 'Mínimo 6 caracteres' ?>">
			<a href="javascript:;" class="input-group-text bg-transparent"><i class='bx bx-hide'></i></a>
		</div>
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'password']) ?>
	</div>
	<div class="col-md-6">
		<label for="password_confirm" class="form-label">Confirmar Contraseña</label>
		<input type="password" class="form-control <?= !empty($errors['password_confirm'] ?? null) ? 'is-invalid' : '' ?>"
			   id="password_confirm" name="password_confirm" placeholder="Repite la contraseña">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'password_confirm']) ?>
	</div>

	<div class="col-12">
		<label class="form-label">Roles <span class="text-danger">*</span></label>
		<div class="border rounded p-3 <?= !empty($errors['roles'] ?? null) ? 'border-danger' : '' ?>">
			<div class="row g-2">
				<?php foreach ($roles as $role): ?>
					<div class="col-md-6 col-lg-3">
						<div class="form-check">
							<input class="form-check-input" type="checkbox" name="roles[]"
								   value="<?= $role['id'] ?>" id="role_<?= $role['id'] ?>"
								   <?= in_array($role['id'], (array) $currentRoles) ? 'checked' : '' ?>>
							<label class="form-check-label" for="role_<?= $role['id'] ?>">
								<strong><?= esc($role['name']) ?></strong>
								<small class="d-block text-muted"><?= esc($role['description']) ?></small>
							</label>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'roles']) ?>
	</div>

	<div class="col-12">
		<hr>
		<div class="d-flex gap-2">
			<button type="submit" class="btn btn-primary px-4"><i class="bx bx-save"></i> <?= $isEdit ? 'Actualizar' : 'Guardar' ?></button>
			<a href="<?= base_url('usuarios') ?>" class="btn btn-outline-secondary px-4"><i class="bx bx-arrow-back"></i> Volver</a>
		</div>
	</div>
</form>
