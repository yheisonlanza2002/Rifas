<?php
$errors ??= null;
$prize ??= [];
$isEdit ??= false;

$prizeTypes = $prizeTypes ?? [
    'PRIZE' => 'Premio Físico',
    'MONEY' => 'Premio en Dinero',
];
$prizeType = old('prize_type') ?? ($prize['prize_type'] ?? 'PRIZE');
$action ??= base_url('premios');
$raffles ??= [];
$lotteries ??= [];
$statuses ??= [];
$currencies ??= [];
?>

<link rel="stylesheet" href="<?= base_url('assets/css/prizes/prizes.css') ?>">
<script src="<?= base_url('assets/js/prizes/prizes.js') ?>"></script>

<form method="post" action="<?= $action ?>" class="row g-3" enctype="multipart/form-data">
	<?= csrf_field() ?>

	<div class="col-md-6">
		<label for="raffle_id" class="form-label">Rifa <span class="text-danger">*</span></label>
		<?php $raffleId = old('raffle_id') ?? ($prize['raffle_id'] ?? ''); ?>
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
		<label for="lottery_id" class="form-label">Lotería <span class="text-danger">*</span></label>
		<?php $lotteryId = old('lottery_id') ?? ($prize['lottery_id'] ?? ''); ?>
		<select class="form-select <?= !empty($errors['lottery_id'] ?? null) ? 'is-invalid' : '' ?>"
				id="lottery_id" name="lottery_id">
			<option value="">Seleccionar lotería...</option>
			<?php foreach ($lotteries as $loteria): ?>
				<option value="<?= esc($loteria['id']) ?>" <?= $lotteryId == $loteria['id'] ? 'selected' : '' ?>>
					<?= esc($loteria['name']) ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'lottery_id']) ?>
	</div>

	<div class="col-md-4">
		<label for="prize_type" class="form-label">Tipo de Premio <span class="text-danger">*</span></label>
		<select class="form-select <?= !empty($errors['prize_type'] ?? null) ? 'is-invalid' : '' ?>"
				id="prize_type" name="prize_type">
			<?php foreach ($prizeTypes as $key => $label): ?>
				<option value="<?= $key ?>" <?= $prizeType === $key ? 'selected' : '' ?>><?= esc($label) ?></option>
			<?php endforeach; ?>
		</select>
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'prize_type']) ?>
	</div>
	<div class="col-md-4">
		<label for="position" class="form-label">Posición del Sorteo <span class="text-danger">*</span></label>
		<input type="number" class="form-control <?= !empty($errors['position'] ?? null) ? 'is-invalid' : '' ?>"
			   id="position" name="position" placeholder="Ej: 1" min="1"
			   value="<?= esc(old('position') ?? ($prize['position'] ?? '')) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'position']) ?>
	</div>
	<div class="col-md-4">
		<label for="quantity" class="form-label">Cantidad <span class="text-danger">*</span></label>
		<input type="number" class="form-control <?= !empty($errors['quantity'] ?? null) ? 'is-invalid' : '' ?>"
			   id="quantity" name="quantity" placeholder="Ej: 1" min="1"
			   value="<?= esc(old('quantity') ?? ($prize['quantity'] ?? '')) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'quantity']) ?>
	</div>

	<div class="col-md-8">
		<label for="name" class="form-label">Nombre del Premio <span class="text-danger">*</span></label>
		<input type="text" class="form-control <?= !empty($errors['name'] ?? null) ? 'is-invalid' : '' ?>"
			   id="name" name="name" placeholder="Ej: Moto Honda XR 150"
			   value="<?= esc(old('name') ?? ($prize['name'] ?? '')) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'name']) ?>
	</div>
	<div class="col-md-4">
		<label for="value" class="form-label">Valor <span class="text-danger">*</span></label>
		<input type="text" inputmode="decimal" step="0.01" class="form-control <?= !empty($errors['value'] ?? null) ? 'is-invalid' : '' ?>"
			   id="value" name="value" placeholder="Ej: 100.000" min="0" data-format-number
			   value="<?= esc(old('value') ?? ($prize['value'] ?? '')) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'value']) ?>
	</div>

	<div class="col-md-6">
		<label for="description" class="form-label">Descripción</label>
		<textarea class="form-control <?= !empty($errors['description'] ?? null) ? 'is-invalid' : '' ?>"
				  id="description" name="description" rows="2" placeholder="Descripción del premio..."><?= esc(old('description') ?? ($prize['description'] ?? '')) ?></textarea>
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'description']) ?>
	</div>
	<div class="col-md-6">
		<label for="currency" class="form-label">Moneda <span class="text-danger">*</span></label>
		<?php $currency = old('currency') ?? ($prize['currency'] ?? 'COP'); ?>
		<select class="form-select <?= !empty($errors['currency'] ?? null) ? 'is-invalid' : '' ?>"
				id="currency" name="currency">
			<?php foreach ($currencies as $key => $label): ?>
				<option value="<?= $key ?>" <?= $currency === $key ? 'selected' : '' ?>><?= esc($label) ?></option>
			<?php endforeach; ?>
		</select>
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'currency']) ?>
	</div>

	<div class="col-md-6 campo-money">
		<label for="recurrence_days" class="form-label">Recurrencia (días) <span class="text-danger">*</span></label>
		<select class="form-select <?= !empty($errors['recurrence_days'] ?? null) ? 'is-invalid' : '' ?>"
				id="recurrence_days" name="recurrence_days">
			<option value="">Seleccionar...</option>
			<option value="7" <?= (old('recurrence_days') ?? ($prize['recurrence_days'] ?? '')) == 7 ? 'selected' : '' ?>>Cada 7 días</option>
			<option value="15" <?= (old('recurrence_days') ?? ($prize['recurrence_days'] ?? '')) == 15 ? 'selected' : '' ?>>Cada 15 días</option>
			<option value="30" <?= (old('recurrence_days') ?? ($prize['recurrence_days'] ?? '')) == 30 ? 'selected' : '' ?>>Cada 30 días</option>
		</select>
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'recurrence_days']) ?>
	</div>
	<div class="col-md-6 campo-money">
		<label for="draw_date" class="form-label">Fecha del Sorteo <span class="text-danger">*</span></label>
		<input type="date" class="form-control <?= !empty($errors['draw_date'] ?? null) ? 'is-invalid' : '' ?>"
			   id="draw_date" name="draw_date"
			   value="<?= esc(old('draw_date') ?? ($prize['draw_date'] ?? '')) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'draw_date']) ?>
	</div>

<div class="col-md-6">
		<label for="prize_image" class="form-label">Imagen del Premio</label>
		<input type="file" class="form-control <?= !empty($errors['prize_image'] ?? null) ? 'is-invalid' : '' ?>"
			   id="prize_image" name="prize_image" accept="image/jpeg,image/png,image/webp">
		<div class="form-text">Imagen del premio (JPG, PNG, WebP - Máx 2MB)</div>
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'prize_image']) ?>
		<?php if (!empty($prize['prize_image'])): ?>
			<div class="mt-2">
				<img src="<?= base_url('uploads/prizes/' . $prize['prize_image']) ?>" alt="Premio" class="img-thumbnail" style="max-height: 100px;">
			</div>
		<?php endif; ?>
	</div>
	<div class="col-md-6">
		<label for="status" class="form-label">Estado <span class="text-danger">*</span></label>
		<?php $status = old('status') ?? ($prize['status'] ?? 'ACTIVE'); ?>
		<select class="form-select <?= !empty($errors['status'] ?? null) ? 'is-invalid' : '' ?>"
				id="status" name="status">
			<?php foreach ($statuses as $key => $label): ?>
				<option value="<?= $key ?>" <?= $status === $key ? 'selected' : '' ?>><?= esc($label) ?></option>
			<?php endforeach; ?>
		</select>
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'status']) ?>
	</div>

	<div class="col-12">
		<div class="card bg-light">
			<div class="card-body">
				<h6 class="mb-2"><i class="bx bx-show"></i> Vista Previa</h6>
				<div id="vista-previa" class="small text-muted">
					Completa los campos para ver la vista previa
				</div>
			</div>
		</div>
	</div>

	<div class="col-12">
		<div class="d-flex justify-content-between align-items-center mb-3">
			<h6 class="mb-0"><i class="bx bx-gift"></i> Premios Adicionales</h6>
			<button type="button" class="btn btn-sm btn-outline-primary" id="btn-agregar-premio">
				<i class="bx bx-plus"></i> Agregar Premio
			</button>
		</div>
		<div id="contenedor-premios"></div>
		<template id="template-premio">
			<div class="prize-item border rounded p-3 mb-3 bg-light">
				<div class="d-flex justify-content-between align-items-center mb-2">
					<span class="badge bg-primary prize-num">Premio #2</span>
					<button type="button" class="btn btn-sm btn-outline-danger btn-eliminar-premio">
						<i class="bx bx-trash"></i>
					</button>
				</div>
				<div class="row g-2">
					<div class="col-md-4">
						<label class="form-label">Nombre <span class="text-danger">*</span></label>
						<input type="text" class="form-control" name="additional_prizes[INDEX][name]" placeholder="Ej: Moto Honda XR 150">
					</div>
					<div class="col-md-3">
						<label class="form-label">Tipo <span class="text-danger">*</span></label>
						<select class="form-select" name="additional_prizes[INDEX][type]">
							<option value="PRIZE">Premio Físico</option>
							<option value="MONEY">Premio en Dinero</option>
						</select>
					</div>
					<div class="col-md-3">
						<label class="form-label">Valor <span class="text-danger">*</span></label>
						<input type="number" class="form-control" name="additional_prizes[INDEX][value]" placeholder="Ej: 100000" min="0" data-format-number>
					</div>
					<div class="col-md-2">
						<label class="form-label">Lotería <span class="text-danger">*</span></label>
						<select class="form-select" name="additional_prizes[INDEX][lottery_id]">
							<option value="">Seleccionar...</option>
							<?php foreach ($lotteries ?? [] as $loteria): ?>
								<option value="<?= esc($loteria['id']) ?>"><?= esc($loteria['name']) ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="col-md-4 prize-money-fields" style="display:none;">
						<label class="form-label">Recurrencia (días)</label>
						<select class="form-select" name="additional_prizes[INDEX][recurrence_days]">
							<option value="">Seleccionar...</option>
							<option value="7">Cada 7 días</option>
							<option value="15">Cada 15 días</option>
							<option value="30">Cada 30 días</option>
						</select>
					</div>
					<div class="col-md-4 prize-money-fields" style="display:none;">
						<label class="form-label">Fecha del Sorteo</label>
						<input type="date" class="form-control" name="additional_prizes[INDEX][draw_date]">
					</div>
					<div class="col-md-4">
						<label class="form-label">Imagen</label>
						<input type="file" class="form-control" name="additional_prizes[INDEX][image]" accept="image/jpeg,image/png,image/webp">
					</div>
				</div>
			</div>
		</template>
	</div>

	<div class="col-12">
		<hr>
		<div class="d-flex gap-2">
			<button type="submit" class="btn btn-primary px-4"><i class="bx bx-save"></i> <?= $isEdit ? 'Actualizar' : 'Guardar' ?></button>
			<a href="<?= base_url('premios') ?>" class="btn btn-outline-secondary px-4"><i class="bx bx-arrow-back"></i> Volver</a>
		</div>
	</div>
</form>
