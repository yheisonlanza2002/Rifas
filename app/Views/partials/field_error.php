<?php
/**
 * Partial para mostrar errores de validación de un campo.
 *
 * Uso en la vista:
 *   <?= view('partials/field_error', ['errors' => $errors ?? null, 'field' => 'email']) ?>
 *
 * El controlador debe enviar los errores con:
 *   ->with('errors', $this->validator->getErrors())
 */

if (!empty($errors) && isset($errors[$field])): ?>
	<div class="invalid-feedback d-block">
		<?= esc($errors[$field]) ?>
	</div>
<?php endif;
