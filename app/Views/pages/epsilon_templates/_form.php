<form action="<?= isset($template) ? base_url('plantillas-email/' . $template['id']) : base_url('plantillas-email') ?>"
	  method="POST" class="row g-3">
	<?= csrf_field() ?>
	<?php if (isset($template)): ?>
		<input type="hidden" name="_method" value="PUT">
	<?php endif; ?>

	<div class="col-md-6">
		<label for="name" class="form-label">Nombre <span class="text-danger">*</span></label>
		<input type="text" name="name" id="name"
			   class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
			   value="<?= isset($template) ? esc($template['name']) : old('name') ?>"
			   placeholder="Ej: Confirmación de Venta" required>
		<?php if (isset($errors['name'])): ?>
			<div class="invalid-feedback"><?= $errors['name'] ?></div>
		<?php endif; ?>
	</div>

	<div class="col-md-6">
		<label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
		<input type="text" name="slug" id="slug"
			   class="form-control <?= isset($errors['slug']) ? 'is-invalid' : '' ?>"
			   value="<?= isset($template) ? esc($template['slug']) : old('slug') ?>"
			   placeholder="Ej: sale-confirmation" required>
		<div class="form-text">Identificador único para usar en el código.</div>
		<?php if (isset($errors['slug'])): ?>
			<div class="invalid-feedback"><?= $errors['slug'] ?></div>
		<?php endif; ?>
	</div>

	<div class="col-md-12">
		<label for="subject" class="form-label">Asunto <span class="text-danger">*</span></label>
		<input type="text" name="subject" id="subject"
			   class="form-control <?= isset($errors['subject']) ? 'is-invalid' : '' ?>"
			   value="<?= isset($template) ? esc($template['subject']) : old('subject') ?>"
			   placeholder="Ej: Tu compra ha sido confirmada" required>
		<?php if (isset($errors['subject'])): ?>
			<div class="invalid-feedback"><?= $errors['subject'] ?></div>
		<?php endif; ?>
	</div>

	<div class="col-md-12">
		<label for="body_html" class="form-label">Cuerpo HTML <span class="text-danger">*</span></label>
		<textarea name="body_html" id="body_html" rows="10"
				  class="form-control <?= isset($errors['body_html']) ? 'is-invalid' : '' ?>"
				  placeholder="HTML del email..." required><?= isset($template) ? esc($template['body_html']) : old('body_html') ?></textarea>
		<div class="form-text">Usa variables como {name}, {sale_code}, etc.</div>
		<?php if (isset($errors['body_html'])): ?>
			<div class="invalid-feedback"><?= $errors['body_html'] ?></div>
		<?php endif; ?>
	</div>

	<div class="col-md-12">
		<label for="body_text" class="form-label">Cuerpo Texto (opcional)</label>
		<textarea name="body_text" id="body_text" rows="5"
				  class="form-control <?= isset($errors['body_text']) ? 'is-invalid' : '' ?>"
				  placeholder="Versión en texto plano..."><?= isset($template) ? esc($template['body_text']) : old('body_text') ?></textarea>
		<?php if (isset($errors['body_text'])): ?>
			<div class="invalid-feedback"><?= $errors['body_text'] ?></div>
		<?php endif; ?>
	</div>

	<div class="col-md-6">
		<label for="variables" class="form-label">Variables Disponibles</label>
		<input type="text" name="variables" id="variables"
			   class="form-control <?= isset($errors['variables']) ? 'is-invalid' : '' ?>"
			   value="<?= isset($template) ? esc($template['variables']) : old('variables') ?>"
			   placeholder="Ej: name, sale_code, ticket_number">
		<div class="form-text">Lista separada por comas.</div>
		<?php if (isset($errors['variables'])): ?>
			<div class="invalid-feedback"><?= $errors['variables'] ?></div>
		<?php endif; ?>
	</div>

	<div class="col-md-3">
		<label for="is_global" class="form-label">Tipo <span class="text-danger">*</span></label>
		<select name="is_global" id="is_global" class="form-select <?= isset($errors['is_global']) ? 'is-invalid' : '' ?>" required>
			<option value="0" <?= (isset($template) ? $template['is_global'] : old('is_global')) == 0 ? 'selected' : '' ?>>Empresa</option>
			<option value="1" <?= (isset($template) ? $template['is_global'] : old('is_global')) == 1 ? 'selected' : '' ?>>Global</option>
		</select>
		<?php if (isset($errors['is_global'])): ?>
			<div class="invalid-feedback"><?= $errors['is_global'] ?></div>
		<?php endif; ?>
	</div>

	<div class="col-md-3">
		<label for="status" class="form-label">Estado <span class="text-danger">*</span></label>
		<select name="status" id="status" class="form-select <?= isset($errors['status']) ? 'is-invalid' : '' ?>" required>
			<?php foreach ($statuses as $key => $label): ?>
				<option value="<?= $key ?>"
					<?= (isset($template) ? $template['status'] : old('status')) == $key ? 'selected' : '' ?>>
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
			<i class="bx bx-save"></i> <?= isset($template) ? 'Actualizar' : 'Guardar' ?>
		</button>
		<a href="<?= base_url('plantillas-email') ?>" class="btn btn-outline-secondary px-4">Cancelar</a>
	</div>
</form>
