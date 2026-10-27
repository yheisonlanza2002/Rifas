<?php
$errors ??= null;
$raffle ??= [];
$isEdit ??= false;
$action ??= base_url('rifas');
$statuses ??= [];
$assignmentModes ??= [];
$selectionModes ??= [];
$currencies ??= [];

$tiposGeneracion = [
    'SEQUENTIAL' => 'Secuencial',
    'RANDOM'     => 'Aleatorio',
];
?>

<form method="post" action="<?= $action ?>" class="row g-3">
	<?= csrf_field() ?>

	<div class="col-md-8">
		<label for="name" class="form-label">Nombre de la Rifa <span class="text-danger">*</span></label>
		<input type="text" class="form-control <?= !empty($errors['name'] ?? null) ? 'is-invalid' : '' ?>"
			   id="name" name="name" placeholder="Ej: Gran Rifa Navideña 2026"
			   value="<?= esc(old('name') ?? ($raffle['name'] ?? '')) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'name']) ?>
	</div>
	<div class="col-md-4">
		<label for="code" class="form-label">Código</label>
		<input type="text" class="form-control" id="code" name="code"
			   value="<?= esc($raffle['code'] ?? '') ?>" readonly>
		<div class="form-text">Se genera automáticamente</div>
	</div>
	<div class="modal fade" id="modal-loteria" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Nueva Lotería</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
				</div>
				<div class="modal-body">
					<div class="mb-3">
						<label for="nueva-loteria-nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="nueva-loteria-nombre" placeholder="Ej: Lotería del Huila">
					</div>
					<div class="mb-3">
						<label for="nueva-loteria-codigo" class="form-label">Código</label>
						<input type="text" class="form-control" id="nueva-loteria-codigo" placeholder="Se genera automáticamente" readonly>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
					<button type="button" class="btn btn-primary" id="guardar-loteria">Guardar</button>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-8">
		<label for="description" class="form-label">Descripción</label>
		<textarea class="form-control <?= !empty($errors['description'] ?? null) ? 'is-invalid' : '' ?>"
				  id="description" name="description" rows="2" placeholder="Descripción de la rifa..."><?= esc(old('description') ?? ($raffle['description'] ?? '')) ?></textarea>
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'description']) ?>
	</div>
	<div class="col-md-4">
		<label for="lottery_id" class="form-label">Lotería <span class="text-danger">*</span></label>
		<div class="input-group">
			<select class="form-select <?= !empty($errors['lottery_id'] ?? null) ? 'is-invalid' : '' ?>"
					id="lottery_id" name="lottery_id">
				<option value="">Seleccionar lotería...</option>
				<?php foreach ($lotteries ?? [] as $loteria): ?>
					<option value="<?= $loteria['id'] ?>"
						<?= (old('lottery_id') ?? ($raffle['lottery_id'] ?? '')) == $loteria['id'] ? 'selected' : '' ?>>
						<?= esc($loteria['name']) ?>
					</option>
				<?php endforeach; ?>
			</select>
			<button type="button" class="btn btn-outline-primary" id="btn-nueva-loteria" title="Agregar lotería">
				<i class="bx bx-plus"></i>
			</button>
		</div>
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'lottery_id']) ?>
	</div>

	<div class="col-md-4">
		<label for="status" class="form-label">Estado <span class="text-danger">*</span></label>
		<?php $status = old('status') ?? ($raffle['status'] ?? 'DRAFT'); ?>
		<select class="form-select <?= !empty($errors['status'] ?? null) ? 'is-invalid' : '' ?>"
				id="status" name="status">
			<?php foreach ($statuses as $key => $label): ?>
				<option value="<?= $key ?>" <?= $status === $key ? 'selected' : '' ?>><?= esc($label) ?></option>
			<?php endforeach; ?>
		</select>
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'status']) ?>
	</div>
	<div class="col-md-4">
		<label for="numbers_per_ticket" class="form-label">Oportunidades de Ganar <span class="text-danger">*</span></label>
		<input type="number" class="form-control <?= !empty($errors['numbers_per_ticket'] ?? null) ? 'is-invalid' : '' ?>"
			   id="numbers_per_ticket" name="numbers_per_ticket" placeholder="Ej: 2" min="1" data-format-number
			   value="<?= esc(old('numbers_per_ticket') ?? '') ?>">
		<div class="form-text">Números que puede tener cada boleta</div>
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'numbers_per_ticket']) ?>
	</div>
	<div class="col-md-4">
		<label for="number_length" class="form-label" id="label-digitos">Juega con las dígitos de la lotería <span class="text-danger">*</span></label>
		<input type="number" class="form-control <?= !empty($errors['number_length'] ?? null) ? 'is-invalid' : '' ?>"
			   id="number_length" name="number_length" placeholder="Ej: 4" min="1" max="9" data-format-number
			   value="<?= esc(old('number_length') ?? '') ?>">
		<div class="form-text">Máximo 9 dígitos</div>
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'number_length']) ?>
	</div>
	<div class="col-md-4">
		<label for="ticket_quantity" class="form-label">Cantidad de Boletas <span class="text-danger">*</span></label>
		<input type="number" class="form-control <?= !empty($errors['ticket_quantity'] ?? null) ? 'is-invalid' : '' ?>"
			   id="ticket_quantity" name="ticket_quantity" placeholder="Ej: 1000" min="1"
			   value="<?= esc(old('ticket_quantity') ?? '') ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'ticket_quantity']) ?>
	</div>
	<div class="col-md-4">
		<label for="tipo_generacion" class="form-label">Tipo de Generación <span class="text-danger">*</span></label>
		<?php $tipoGeneracion = old('tipo_generacion') ?? ($raffle['number_assignment_mode'] ?? 'RANDOM'); ?>
		<select class="form-select <?= !empty($errors['tipo_generacion'] ?? null) ? 'is-invalid' : '' ?>"
				id="tipo_generacion" name="tipo_generacion">
			<?php foreach ($tiposGeneracion as $key => $label): ?>
				<option value="<?= $key ?>" <?= $tipoGeneracion === $key ? 'selected' : '' ?>><?= esc($label) ?></option>
			<?php endforeach; ?>
		</select>
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'tipo_generacion']) ?>
	</div>
	<div class="col-md-4">
		<label for="number_start" class="form-label">Número Inicial</label>
		<input type="text" class="form-control" id="number_start" name="number_start"
			   value="<?= esc(old('number_start') ?? ($raffle['number_start'] ?? '')) ?>" readonly>
		<div class="form-text">Calculado automáticamente</div>
	</div>

	<div class="col-md-4">
		<label for="number_end" class="form-label">Número Final</label>
		<input type="text" class="form-control" id="number_end" name="number_end"
			   value="<?= esc(old('number_end') ?? ($raffle['number_end'] ?? '')) ?>" readonly>
		<div class="form-text">Calculado automáticamente</div>
	</div>
	<div class="col-md-4">
		<label for="currency" class="form-label">Moneda <span class="text-danger">*</span></label>
		<?php $currency = old('currency') ?? ($raffle['currency'] ?? 'COP'); ?>
		<select class="form-select <?= !empty($errors['currency'] ?? null) ? 'is-invalid' : '' ?>"
				id="currency" name="currency">
			<?php foreach ($currencies as $key => $label): ?>
				<option value="<?= $key ?>" <?= $currency === $key ? 'selected' : '' ?>><?= esc($label) ?></option>
			<?php endforeach; ?>
		</select>
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'currency']) ?>
	</div>
	<div class="col-md-4">
		<label for="ticket_price" class="form-label">Precio por Boleta <span class="text-danger">*</span></label>
		<input type="text" inputmode="decimal" step="0.01" class="form-control <?= !empty($errors['ticket_price'] ?? null) ? 'is-invalid' : '' ?>"
			   id="ticket_price" name="ticket_price" placeholder="Ej: 5.000" min="0.01" data-format-number
			   value="<?= esc(old('ticket_price') ?? ($raffle['ticket_price'] ?? '')) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'ticket_price']) ?>
	</div>
	<div class="col-md-4">
		<label for="commission_rate" class="form-label">Comisión (%)</label>
		<input type="number" step="0.01" class="form-control <?= !empty($errors['commission_rate'] ?? null) ? 'is-invalid' : '' ?>"
			   id="commission_rate" name="commission_rate" placeholder="Ej: 5.00" min="0" max="100" data-format-number
			   value="<?= esc(old('commission_rate') ?? ($raffle['commission_rate'] ?? 0)) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'commission_rate']) ?>
	</div>
	<div class="col-md-4">
		<label for="reservation_minutes" class="form-label">Minutos de Reserva</label>
		<input type="number" class="form-control <?= !empty($errors['reservation_minutes'] ?? null) ? 'is-invalid' : '' ?>"
			   id="reservation_minutes" name="reservation_minutes" placeholder="Ej: 15" min="1"
			   value="<?= esc(old('reservation_minutes') ?? ($raffle['reservation_minutes'] ?? 15)) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'reservation_minutes']) ?>
	</div>

	<div class="col-md-6">
		<label for="start_at" class="form-label">Fecha de Inicio</label>
		<input type="datetime-local" class="form-control <?= !empty($errors['start_at'] ?? null) ? 'is-invalid' : '' ?>"
			   id="start_at" name="start_at"
			   value="<?= esc(old('start_at') ?? ($raffle['start_at'] ?? '')) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'start_at']) ?>
	</div>
	<div class="col-md-6">
		<label for="end_at" class="form-label">Fecha de Fin</label>
		<input type="datetime-local" class="form-control <?= !empty($errors['end_at'] ?? null) ? 'is-invalid' : '' ?>"
			   id="end_at" name="end_at"
			   value="<?= esc(old('end_at') ?? ($raffle['end_at'] ?? '')) ?>">
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'end_at']) ?>
	</div>

	<div class="col-12">
		<label for="terms_conditions" class="form-label">Términos y Condiciones</label>
		<textarea class="form-control <?= !empty($errors['terms_conditions'] ?? null) ? 'is-invalid' : '' ?>"
				  id="terms_conditions" name="terms_conditions" rows="3" placeholder="Términos y condiciones de la rifa..."><?= esc(old('terms_conditions') ?? ($raffle['terms_conditions'] ?? '')) ?></textarea>
		<?= view('partials/field_error', ['errors' => $errors, 'field' => 'terms_conditions']) ?>
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

	<link rel="stylesheet" href="<?= base_url('assets/css/raffles/raffles.css') ?>">

	<div class="col-12">
		<hr>
		<div class="d-flex gap-2">
			<button type="submit" class="btn btn-primary px-4"><i class="bx bx-save"></i> <?= $isEdit ? 'Actualizar' : 'Guardar' ?></button>
			<a href="<?= base_url('rifas') ?>" class="btn btn-outline-secondary px-4"><i class="bx bx-arrow-back"></i> Volver</a>
		</div>
	</div>
</form>

<?= $this->section('scripts') ?>
<script>
    const RAFFLES_URL_GUARDAR_LOTERIA = '<?= base_url('loterias/guardar') ?>';
</script>
<script src="<?= base_url('assets/js/raffles/raffles.js') ?>"></script>
<?= $this->endSection() ?>
