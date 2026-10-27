<form action="<?= isset($draw) ? base_url('sorteos/' . $draw['id']) : base_url('sorteos') ?>"
	  method="POST" class="row g-3">
	<?= csrf_field() ?>
	<?php if (isset($draw)): ?>
		<input type="hidden" name="_method" value="PUT">
	<?php endif; ?>

	<div class="col-md-12">
		<label for="raffle_id" class="form-label">Rifa <span class="text-danger">*</span></label>
		<select name="raffle_id" id="raffle_id" class="form-select <?= isset($errors['raffle_id']) ? 'is-invalid' : '' ?>" required>
			<option value="">Seleccionar rifa...</option>
			<?php foreach ($raffles as $r): ?>
				<option value="<?= $r['id'] ?>"
					<?= (isset($draw) ? $draw['raffle_id'] : old('raffle_id')) == $r['id'] ? 'selected' : '' ?>>
					<?= esc($r['name']) ?> (<?= esc($r['code']) ?>)
				</option>
			<?php endforeach; ?>
		</select>
		<?php if (isset($errors['raffle_id'])): ?>
			<div class="invalid-feedback"><?= $errors['raffle_id'] ?></div>
		<?php endif; ?>
	</div>

	<div class="col-md-12">
		<label for="name" class="form-label">Nombre del Sorteo <span class="text-danger">*</span></label>
		<input type="text" name="name" id="name"
			   class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
			   value="<?= isset($draw) ? esc($draw['name']) : old('name') ?>"
			   placeholder="Ej: Sorteo de Navidad 2026" required>
		<?php if (isset($errors['name'])): ?>
			<div class="invalid-feedback"><?= $errors['name'] ?></div>
		<?php endif; ?>
	</div>

	<div class="col-md-6">
		<label for="scheduled_at" class="form-label">Fecha Programada <span class="text-danger">*</span></label>
		<input type="datetime-local" name="scheduled_at" id="scheduled_at"
			   class="form-control <?= isset($errors['scheduled_at']) ? 'is-invalid' : '' ?>"
			   value="<?= isset($draw) ? date('Y-m-d\TH:i', strtotime($draw['scheduled_at'])) : old('scheduled_at') ?>"
			   required>
		<?php if (isset($errors['scheduled_at'])): ?>
			<div class="invalid-feedback"><?= $errors['scheduled_at'] ?></div>
		<?php endif; ?>
	</div>

	<div class="col-md-6">
		<label for="method" class="form-label">Método <span class="text-danger">*</span></label>
		<select name="method" id="method" class="form-select <?= isset($errors['method']) ? 'is-invalid' : '' ?>" required>
			<?php foreach ($methods as $key => $label): ?>
				<option value="<?= $key ?>"
					<?= (isset($draw) ? $draw['method'] : old('method')) == $key ? 'selected' : '' ?>>
					<?= $label ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php if (isset($errors['method'])): ?>
			<div class="invalid-feedback"><?= $errors['method'] ?></div>
		<?php endif; ?>
	</div>

	<div class="col-md-6">
		<label for="seed" class="form-label">Semilla (opcional)</label>
		<input type="text" name="seed" id="seed"
			   class="form-control <?= isset($errors['seed']) ? 'is-invalid' : '' ?>"
			   value="<?= isset($draw) ? esc($draw['seed']) : old('seed') ?>"
			   placeholder="Para reproducibilidad">
		<div class="form-text">Si se deja vacío, se generará una semilla aleatoria.</div>
		<?php if (isset($errors['seed'])): ?>
			<div class="invalid-feedback"><?= $errors['seed'] ?></div>
		<?php endif; ?>
	</div>

	<div class="col-md-6">
		<label for="external_reference" class="form-label">Referencia Externa (opcional)</label>
		<input type="text" name="external_reference" id="external_reference"
			   class="form-control <?= isset($errors['external_reference']) ? 'is-invalid' : '' ?>"
			   value="<?= isset($draw) ? esc($draw['external_reference']) : old('external_reference') ?>"
			   placeholder="ID o referencia externa">
		<?php if (isset($errors['external_reference'])): ?>
			<div class="invalid-feedback"><?= $errors['external_reference'] ?></div>
		<?php endif; ?>
	</div>

	<div class="col-12">
		<button type="submit" class="btn btn-primary px-4">
			<i class="bx bx-save"></i> <?= isset($draw) ? 'Actualizar' : 'Guardar' ?>
		</button>
		<a href="<?= base_url('sorteos') ?>" class="btn btn-outline-secondary px-4">Cancelar</a>
	</div>
</form>
