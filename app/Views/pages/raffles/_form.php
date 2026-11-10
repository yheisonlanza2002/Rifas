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
    'RANDOM'     => 'Aleatorio (Recomendado)',
    'SEQUENTIAL' => 'Secuencial',
];

$precioVal = old('ticket_price') ?? (!empty($raffle['ticket_price']) ? number_format((float)$raffle['ticket_price'], 0, ',', '.') : '');
$startAtVal = old('start_at') ?? (!empty($raffle['start_at']) ? date('Y-m-d\TH:i', strtotime($raffle['start_at'])) : '');
$endAtVal = old('end_at') ?? (!empty($raffle['end_at']) ? date('Y-m-d\TH:i', strtotime($raffle['end_at'])) : '');
?>

<link rel="stylesheet" href="<?= base_url('assets/css/raffles/raffles.css') ?>">

<form method="post" action="<?= $action ?>">
	<?= csrf_field() ?>

	<div class="row g-4">
		<!-- ==========================================
		     COLUMNA IZQUIERDA: FORMULARIO PRINCIPAL
		     ========================================== -->
		<div class="col-lg-7">
			<!-- 1. Información General -->
			<div class="form-card mb-4">
				<div class="card-header py-3 d-flex align-items-center justify-content-between">
					<h6 class="mb-0 fw-bold text-primary">
						<i class="bx bx-info-circle me-1"></i> Información General
					</h6>
					<span class="badge bg-light text-muted border">Paso 1</span>
				</div>
				<div class="card-body p-3 p-md-4">
					<div class="row g-3">
						<div class="col-md-8">
							<label for="name" class="form-label">Nombre de la Rifa <span class="text-danger">*</span></label>
							<input type="text" class="form-control <?= !empty($errors['name'] ?? null) ? 'is-invalid' : '' ?>"
								   id="name" name="name" placeholder="Ej: Gran Rifa Navideña 2026"
								   value="<?= esc(old('name') ?? ($raffle['name'] ?? '')) ?>" required>
							<?= view('partials/field_error', ['errors' => $errors, 'field' => 'name']) ?>
						</div>

						<div class="col-md-4">
							<label for="code" class="form-label">Código de Rifa</label>
							<div class="input-group">
								<span class="input-group-text bg-light text-muted"><i class="bx bx-barcode"></i></span>
								<input type="text" class="form-control" id="code" name="code"
									   value="<?= esc(old('code') ?? ($raffle['code'] ?? '')) ?>" readonly>
							</div>
							<div class="form-text">Generado automáticamente</div>
						</div>

						<div class="col-12">
							<label for="description" class="form-label">Descripción pública</label>
							<textarea class="form-control <?= !empty($errors['description'] ?? null) ? 'is-invalid' : '' ?>"
									  id="description" name="description" rows="2"
									  placeholder="Describe brevemente la causa, los premios o los detalles atractivos de la rifa..."><?= esc(old('description') ?? ($raffle['description'] ?? '')) ?></textarea>
							<?= view('partials/field_error', ['errors' => $errors, 'field' => 'description']) ?>
						</div>

						<div class="col-md-8">
							<label for="lottery_id" class="form-label">Lotería de Juego <span class="text-danger">*</span></label>
							<div class="input-group">
								<span class="input-group-text bg-light"><i class="bx bx-dice-5 text-primary"></i></span>
								<select class="form-select <?= !empty($errors['lottery_id'] ?? null) ? 'is-invalid' : '' ?>"
										id="lottery_id" name="lottery_id" required>
									<option value="">Seleccionar lotería oficial...</option>
									<?php foreach ($lotteries ?? [] as $loteria): ?>
										<option value="<?= $loteria['id'] ?>"
											<?= (old('lottery_id') ?? ($raffle['lottery_id'] ?? '')) == $loteria['id'] ? 'selected' : '' ?>>
											<?= esc($loteria['name']) ?>
										</option>
									<?php endforeach; ?>
								</select>
								<button type="button" class="btn btn-primary" id="btn-nueva-loteria" title="Crear nueva lotería rápida">
									<i class="bx bx-plus"></i> Nueva
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
					</div>
				</div>
			</div>

			<!-- 2. Configuración de Boletas y Lógica de Sorteo -->
			<div class="form-card mb-4">
				<div class="card-header py-3 d-flex align-items-center justify-content-between">
					<h6 class="mb-0 fw-bold text-primary">
						<i class="bx bx-cog me-1"></i> Configuración de Boletas y Lotería
					</h6>
					<span class="badge bg-light text-muted border">Paso 2</span>
				</div>
				<div class="card-body p-3 p-md-4">
					<div class="row g-3">
						<div class="col-md-4">
							<label for="ticket_quantity" class="form-label">Cantidad de Boletas <span class="text-danger">*</span></label>
							<div class="input-group">
								<span class="input-group-text bg-light"><i class="bx bx-receipt"></i></span>
								<input type="number" class="form-control <?= !empty($errors['ticket_quantity'] ?? null) ? 'is-invalid' : '' ?>"
									   id="ticket_quantity" name="ticket_quantity" placeholder="Ej: 1000" min="1"
									   value="<?= esc(old('ticket_quantity') ?? ($raffle['ticket_quantity'] ?? '')) ?>" required>
							</div>
							<?= view('partials/field_error', ['errors' => $errors, 'field' => 'ticket_quantity']) ?>
						</div>

						<div class="col-md-4">
							<label for="numbers_per_ticket" class="form-label">Oportunidades de Ganar <span class="text-danger">*</span></label>
							<div class="input-group">
								<span class="input-group-text bg-light"><i class="bx bx-hash"></i></span>
								<input type="number" class="form-control <?= !empty($errors['numbers_per_ticket'] ?? null) ? 'is-invalid' : '' ?>"
									   id="numbers_per_ticket" name="numbers_per_ticket" placeholder="Ej: 2" min="1"
									   value="<?= esc(old('numbers_per_ticket') ?? ($raffle['numbers_per_ticket'] ?? 1)) ?>" required>
							</div>
							<div class="form-text">Números por boleta</div>
							<?= view('partials/field_error', ['errors' => $errors, 'field' => 'numbers_per_ticket']) ?>
						</div>

						<div class="col-md-4">
							<label for="number_length" class="form-label" id="label-digitos">
								Cifras de Lotería <span class="text-danger">*</span>
							</label>
							<div class="input-group">
								<span class="input-group-text bg-light"><i class="bx bx-target-lock"></i></span>
								<input type="number" class="form-control <?= !empty($errors['number_length'] ?? null) ? 'is-invalid' : '' ?>"
									   id="number_length" name="number_length" placeholder="Ej: 4" min="1" max="9"
									   value="<?= esc(old('number_length') ?? ($raffle['number_length'] ?? 4)) ?>" required>
							</div>
							<div class="form-text">Mínimo para la cantidad</div>
							<?= view('partials/field_error', ['errors' => $errors, 'field' => 'number_length']) ?>
						</div>

						<div class="col-md-4">
							<label for="tipo_generacion" class="form-label">Asignación de Números <span class="text-danger">*</span></label>
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
							<input type="text" class="form-control bg-light" id="number_start" name="number_start"
								   value="<?= esc(old('number_start') ?? ($raffle['number_start'] ?? '')) ?>" readonly>
							<div class="form-text">Calculado automático</div>
						</div>

						<div class="col-md-4">
							<label for="number_end" class="form-label">Número Final</label>
							<input type="text" class="form-control bg-light" id="number_end" name="number_end"
								   value="<?= esc(old('number_end') ?? ($raffle['number_end'] ?? '')) ?>" readonly>
							<div class="form-text">Calculado automático</div>
						</div>
					</div>
				</div>
			</div>

			<!-- 3. Precios, Fechas y Condiciones -->
			<div class="form-card mb-4">
				<div class="card-header py-3 d-flex align-items-center justify-content-between">
					<h6 class="mb-0 fw-bold text-primary">
						<i class="bx bx-dollar-circle me-1"></i> Precios, Fechas y Condiciones
					</h6>
					<span class="badge bg-light text-muted border">Paso 3</span>
				</div>
				<div class="card-body p-3 p-md-4">
					<div class="row g-3">
						<div class="col-md-4">
							<label for="ticket_price" class="form-label">Precio por Boleta <span class="text-danger">*</span></label>
							<div class="input-group">
								<span class="input-group-text bg-light text-success fw-bold">$</span>
								<input type="text" inputmode="numeric" class="form-control <?= !empty($errors['ticket_price'] ?? null) ? 'is-invalid' : '' ?>"
									   id="ticket_price" name="ticket_price" placeholder="Ej: 20.000" data-format-number
									   value="<?= esc($precioVal) ?>" required>
							</div>
							<?= view('partials/field_error', ['errors' => $errors, 'field' => 'ticket_price']) ?>
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
							<label for="reservation_minutes" class="form-label">Tiempo de Reserva</label>
							<div class="input-group">
								<input type="number" class="form-control <?= !empty($errors['reservation_minutes'] ?? null) ? 'is-invalid' : '' ?>"
									   id="reservation_minutes" name="reservation_minutes" placeholder="Ej: 15" min="1"
									   value="<?= esc(old('reservation_minutes') ?? ($raffle['reservation_minutes'] ?? 15)) ?>">
								<span class="input-group-text bg-light">min</span>
							</div>
							<?= view('partials/field_error', ['errors' => $errors, 'field' => 'reservation_minutes']) ?>
						</div>

						<div class="col-md-6">
							<label for="start_at" class="form-label">Fecha y Hora de Inicio</label>
							<div class="input-group">
								<span class="input-group-text bg-light"><i class="bx bx-calendar"></i></span>
								<input type="datetime-local" class="form-control <?= !empty($errors['start_at'] ?? null) ? 'is-invalid' : '' ?>"
									   id="start_at" name="start_at"
									   value="<?= esc($startAtVal) ?>">
							</div>
							<?= view('partials/field_error', ['errors' => $errors, 'field' => 'start_at']) ?>
						</div>

						<div class="col-md-6">
							<label for="end_at" class="form-label">Fecha y Hora del Sorteo <span class="text-danger">*</span></label>
							<div class="input-group">
								<span class="input-group-text bg-light text-primary"><i class="bx bx-calendar-check"></i></span>
								<input type="datetime-local" class="form-control <?= !empty($errors['end_at'] ?? null) ? 'is-invalid' : '' ?>"
									   id="end_at" name="end_at"
									   value="<?= esc($endAtVal) ?>" required>
							</div>
							<?= view('partials/field_error', ['errors' => $errors, 'field' => 'end_at']) ?>
						</div>

						<div class="col-12">
							<label for="terms_conditions" class="form-label">Términos, Condiciones y Reglas de Entrega</label>
							<textarea class="form-control <?= !empty($errors['terms_conditions'] ?? null) ? 'is-invalid' : '' ?>"
									  id="terms_conditions" name="terms_conditions" rows="3"
									  placeholder="Detalla cómo se reclama el premio, qué sucede en caso de que no caiga la cifra, condiciones de envío, etc."><?= esc(old('terms_conditions') ?? ($raffle['terms_conditions'] ?? '')) ?></textarea>
							<?= view('partials/field_error', ['errors' => $errors, 'field' => 'terms_conditions']) ?>
						</div>
					</div>
				</div>
			</div>

			<!-- Botones de Acción -->
			<div class="d-flex align-items-center gap-2 pt-2 mb-4">
				<button type="submit" class="btn btn-primary px-4 py-2 fw-bold shadow-sm">
					<i class="bx bx-save me-1"></i> <?= $isEdit ? 'Actualizar Rifa' : 'Guardar y Continuar' ?>
				</button>
				<a href="<?= base_url('rifas') ?>" class="btn btn-outline-secondary px-4 py-2">
					<i class="bx bx-arrow-back me-1"></i> Volver al Listado
				</a>
			</div>
		</div>

		<!-- ==========================================
		     COLUMNA DERECHA: VISTA PREVIA EN VIVO
		     ========================================== -->
		<div class="col-lg-5">
			<div class="preview-sticky">
				<div class="ticket-preview-wrapper mb-3">
					<div class="p-3 bg-light border-bottom d-flex align-items-center justify-content-between">
						<div class="d-flex align-items-center gap-2">
							<span class="badge bg-primary text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
								<i class="bx bx-show fs-5"></i>
							</span>
							<div>
								<h6 class="mb-0 fw-bold text-dark">Boleta Digital en Vivo</h6>
								<small class="text-muted">Se sincroniza al instante mientras escribes</small>
							</div>
						</div>
						<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
							<i class="bx bx-radio-circle-marked me-1"></i> Activo
						</span>
					</div>

					<div class="p-3 bg-white">
						<div id="vista-previa">
							<!-- Generado en tiempo real por raffles.js -->
						</div>
					</div>
				</div>

				<div class="card border-0 bg-light-subtle shadow-none border">
					<div class="card-body p-3">
						<h6 class="fw-bold fs-7 text-dark mb-2">
							<i class="bx bx-bulb text-warning me-1"></i> ¿Cómo funciona el cálculo?
						</h6>
						<p class="small text-muted mb-0">
							El número de boletas multiplicado por las oportunidades de ganar define el total de números en juego. El sistema ajustará automáticamente los dígitos necesarios para que ninguna boleta quede sin combinaciones.
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>

<!-- Modal para Nueva Lotería Rápida -->
<div class="modal fade" id="modal-loteria" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content shadow-lg border-0">
			<div class="modal-header bg-primary text-white">
				<h5 class="modal-title text-white"><i class="bx bx-dice-5 me-1"></i> Nueva Lotería</h5>
				<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
			</div>
			<div class="modal-body p-4">
				<div class="mb-3">
					<label for="nueva-loteria-nombre" class="form-label">Nombre de la Lotería <span class="text-danger">*</span></label>
					<input type="text" class="form-control" id="nueva-loteria-nombre" placeholder="Ej: Lotería de Medellín">
				</div>
				<div class="alert alert-info py-2 small mb-0">
					<i class="bx bx-info-circle me-1"></i> El código de la lotería se generará automáticamente a partir del nombre.
				</div>
			</div>
			<div class="modal-footer bg-light">
				<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
				<button type="button" class="btn btn-primary" id="guardar-loteria">
					<i class="bx bx-check me-1"></i> Guardar Lotería
				</button>
			</div>
		</div>
	</div>
</div>

<script>
    const RAFFLES_URL_GUARDAR_LOTERIA = '<?= base_url('loterias/guardar') ?>';
</script>
<script src="<?= base_url('assets/js/Raffles/raffles.js') ?>"></script>
