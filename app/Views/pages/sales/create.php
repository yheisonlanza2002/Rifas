<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/sales/pos.css') ?>">

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
	<div>
		<h4 class="mb-1">Nueva Venta</h4>
		<p class="text-muted mb-0">Elige la rifa, busca el número del participante y cobra.</p>
	</div>
	<a href="<?= base_url('ventas') ?>" class="btn btn-outline-secondary px-4">
		<i class="bx bx-history me-1"></i> Historial de ventas
	</a>
</div>

<div class="row g-3 pos-layout">
	<div class="col-xl-8">
		<!-- 1. Seleccionar rifa -->
		<div class="card pos-card mb-3">
			<div class="card-header">
				<h6 class="mb-0 fw-bold"><span class="pos-step">1</span> Seleccionar rifa</h6>
			</div>
			<div class="card-body">
				<select id="posRaffle" class="form-select form-select-lg" <?= empty($raffles) ? 'disabled' : '' ?>>
					<option value=""><?= empty($raffles) ? 'No hay rifas activas para vender' : '-- Elige la rifa a vender --' ?></option>
					<?php foreach ($raffles as $r): ?>
						<option value="<?= (int) $r['id'] ?>"><?= esc($r['name']) ?> (<?= esc($r['code']) ?>)</option>
					<?php endforeach; ?>
				</select>

				<div id="rafflePanel" class="d-none">
					<div class="pos-prize mt-3">
						<div class="pos-prize-img">
							<img id="prizeImg" class="d-none" alt="Premio">
							<i id="prizeIcon" class="bx bx-gift"></i>
						</div>
						<div>
							<small class="text-muted text-uppercase fw-semibold">Premio principal</small>
							<div class="fw-bold fs-5 lh-sm" id="prizeName">—</div>
							<div class="pos-prize-value" id="prizeValue">—</div>
						</div>
					</div>

					<div class="pos-chips" id="raffleChips"></div>

					<div class="pos-progress mt-3">
						<div class="d-flex justify-content-between small mb-1 text-muted">
							<span id="progressLabel">—</span>
							<span id="progressPct">0%</span>
						</div>
						<div class="raffle-progress"><div class="raffle-progress-bar" id="progressBar" style="width:0%"></div></div>
					</div>

					<div id="noTickets" class="alert alert-warning mt-3 mb-0 d-none">
						<i class="bx bx-error-circle me-1"></i> Esta rifa aún no tiene boletas generadas. Genera las boletas desde el módulo Boletas antes de vender.
					</div>
				</div>
			</div>
		</div>

		<!-- 2. Buscar número -->
		<div class="card pos-card">
			<div class="card-header d-flex justify-content-between align-items-center">
				<h6 class="mb-0 fw-bold"><span class="pos-step">2</span> Buscar número</h6>
				<small class="text-muted" id="searchHint">El número se rellena con ceros según la rifa</small>
			</div>
			<div class="card-body">
				<div class="input-group input-group-lg pos-search">
					<input type="text" id="posSearch" class="form-control" placeholder="Ej: 1425"
						   inputmode="numeric" autocomplete="off" disabled>
					<button class="btn btn-primary" id="btnSearch" type="button" disabled>
						<i class="bx bx-search me-1"></i>Buscar
					</button>
				</div>
				<div id="searchResult" class="mt-3"></div>
			</div>
		</div>
	</div>

	<!-- 3. Carrito -->
	<div class="col-xl-4">
		<div class="card pos-card pos-cart">
			<div class="card-header d-flex justify-content-between align-items-center">
				<h6 class="mb-0 fw-bold"><span class="pos-step">3</span> Carrito</h6>
				<span class="badge bg-primary rounded-pill" id="cartCount">0</span>
			</div>
			<div class="card-body">
				<div id="cartEmpty" class="text-center text-muted py-4">
					<i class="bx bx-cart d-block fs-1 mb-2"></i>
					<small>Busca números y agrégalos aquí</small>
				</div>

				<div id="cartItems"></div>

				<div class="cart-total mt-2 pt-3 border-top d-none" id="cartTotalWrap">
					<span>Total</span>
					<span id="cartTotal">$ 0</span>
				</div>

				<hr>

				<label class="form-label" for="posParticipant">Participante <span class="text-danger">*</span></label>
				<div class="d-flex gap-2">
					<select id="posParticipant" class="form-select">
						<option value="">-- Seleccionar --</option>
						<?php foreach ($participants as $p): ?>
							<option value="<?= (int) $p['id'] ?>">
								<?= esc(trim($p['name'] . ' ' . ($p['last_name'] ?? ''))) ?> (<?= esc($p['document_number']) ?>)
							</option>
						<?php endforeach; ?>
					</select>
					<button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
							data-bs-target="#modalParticipante" title="Nuevo participante">
						<i class="bx bx-plus"></i>
					</button>
				</div>

				<div class="mt-3">
					<label class="form-label" for="posMethod">Método de pago <span class="text-danger">*</span></label>
					<select id="posMethod" class="form-select">
						<option value="CASH">Efectivo</option>
						<option value="TRANSFER">Transferencia</option>
						<option value="CREDIT">Pagará después (crédito)</option>
					</select>
				</div>

				<div class="mt-3 d-none" id="refGroup">
					<label class="form-label" for="posReference">Referencia de transferencia <span class="text-danger">*</span></label>
					<input type="text" id="posReference" class="form-control" placeholder="Ej: TRX-987654" maxlength="150">
				</div>

				<button type="button" class="btn btn-success btn-lg w-100 mt-4" id="btnConfirm" disabled>
					<i class="bx bx-check-circle me-1"></i>Confirmar venta
				</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal: participante rápido -->
<div class="modal fade" id="modalParticipante" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog">
		<form class="modal-content" id="formRapido" method="post" action="<?= base_url('participantes/rapido') ?>">
			<?= csrf_field() ?>
			<div class="modal-header">
				<h5 class="modal-title"><i class="bx bx-user-plus me-1"></i> Nuevo participante rápido</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body row g-3">
				<div class="col-md-4">
					<label class="form-label">Tipo de doc. <span class="text-danger">*</span></label>
					<select name="document_type" class="form-select" required>
						<option value="CC">Cédula</option>
						<option value="NIT">NIT</option>
						<option value="CE">C. Extranjería</option>
						<option value="TI">Tarjeta ID</option>
						<option value="PAP">Pasaporte</option>
						<option value="PA">Permiso</option>
					</select>
				</div>
				<div class="col-md-8">
					<label class="form-label">Número de documento <span class="text-danger">*</span></label>
					<input type="text" name="document_number" class="form-control" minlength="5" maxlength="50" required>
				</div>
				<div class="col-md-8">
					<label class="form-label">Nombre <span class="text-danger">*</span></label>
					<input type="text" name="name" class="form-control" minlength="3" maxlength="100" required>
				</div>
				<div class="col-md-4">
					<label class="form-label">Teléfono <span class="text-danger">*</span></label>
					<input type="text" name="phone" class="form-control" minlength="7" maxlength="30" required>
				</div>
				<div class="col-12">
					<div class="alert alert-danger py-2 d-none" id="erroresRapido"></div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
				<button type="submit" class="btn btn-primary" id="btnRapidoGuardar">
					<i class="bx bx-save me-1"></i>Guardar
				</button>
			</div>
		</form>
	</div>
</div>

<!-- Formulario oculto del POS -->
<form id="posForm" method="post" action="<?= base_url('ventas') ?>" class="d-none">
	<?= csrf_field() ?>
	<input type="hidden" name="raffle_id" id="fRaffle" value="">
	<input type="hidden" name="participant_id" id="fParticipant" value="">
	<input type="hidden" name="payment_method" id="fMethod" value="">
	<input type="hidden" name="reference" id="fReference" value="">
	<div id="fTickets"></div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
	const $ = (id) => document.getElementById(id);

	const state = {
		raffleId: null,
		raffle: null,
		cart: []
	};

	const money = (v) => '$ ' + Number(v).toLocaleString('es-CO', { maximumFractionDigits: 2 });

	const STATUS = {
		AVAILABLE: { label: 'Disponible', cls: 'bg-success' },
		RESERVED:  { label: 'Reservada', cls: 'bg-warning text-dark' },
		SOLD:      { label: 'Vendida',   cls: 'bg-danger' },
		CANCELLED: { label: 'Cancelada', cls: 'bg-secondary' }
	};

	// ---------- 1. Cargar datos de la rifa ----------
	$('posRaffle').addEventListener('change', function () {
		const id = parseInt(this.value, 10);
		resetSearch();

		if (!id) {
			state.raffleId = null;
			state.raffle = null;
			$('rafflePanel').classList.add('d-none');
			setSearchEnabled(false);
			clearCart(true);
			updateConfirm();
			return;
		}

		if (state.cart.length > 0 && state.raffleId !== id) {
			clearCart(true);
			showToast('Se vació el carrito por cambio de rifa', 'info');
		}

		state.raffleId = id;
		fetch('<?= base_url('ventas/datos-rifa') ?>?raffle_id=' + id)
			.then((r) => r.json())
			.then((data) => {
				if (!data.ok) {
					showToast(data.message || 'No se pudo cargar la rifa', 'error');
					return;
				}
				state.raffle = data.raffle;
				renderRaffle(data);
				setSearchEnabled(data.stats.total > 0);
				updateConfirm();
			})
			.catch(() => showToast('Error cargando la rifa', 'error'));
	});

	function renderRaffle(data) {
		const r = data.raffle;
		const p = data.prize;

		$('rafflePanel').classList.remove('d-none');

		// Premio
		if (p) {
			$('prizeName').textContent = p.name;
			$('prizeValue').textContent = p.type === 'MONEY' ? money(p.value) : (p.value > 0 ? money(p.value) : 'Premio en especie');
			if (p.image) {
				$('prizeImg').src = p.image;
				$('prizeImg').classList.remove('d-none');
				$('prizeIcon').classList.add('d-none');
			} else {
				$('prizeImg').classList.add('d-none');
				$('prizeIcon').classList.remove('d-none');
			}
		} else {
			$('prizeName').textContent = 'Sin premio registrado';
			$('prizeValue').textContent = '—';
			$('prizeImg').classList.add('d-none');
			$('prizeIcon').classList.remove('d-none');
		}

		// Chips
		const chips = [
			{ icon: 'bx-money', label: 'Precio boleta', value: money(r.ticket_price) },
			{ icon: 'bx-trophy', label: 'Oportunidades de ganar', value: r.numbers_per_ticket },
			{ icon: 'bx-hash', label: 'Dígitos', value: r.number_length },
			{ icon: 'bx-ticket', label: 'Boletas', value: r.ticket_quantity.toLocaleString('es-CO') },
			{ icon: 'bx-range', label: 'Rango', value: r.number_start + ' – ' + r.number_end }
		];
		$('raffleChips').innerHTML = chips.map((c) =>
			'<span class="pos-chip"><i class="bx ' + c.icon + '"></i>' + c.label + ': <strong>' + c.value + '</strong></span>'
		).join('');

		// Progreso
		const s = data.stats;
		const vendidas = s.SOLD + s.CANCELLED;
		const pct = s.total > 0 ? Math.round((vendidas * 100) / s.total) : 0;
		$('progressLabel').textContent = vendidas.toLocaleString('es-CO') + ' vendidas de ' + s.total.toLocaleString('es-CO') +
			' · ' + s.AVAILABLE.toLocaleString('es-CO') + ' disponibles';
		$('progressPct').textContent = pct + '%';
		$('progressBar').style.width = pct + '%';

		// Sin boletas
		$('noTickets').classList.toggle('d-none', s.total > 0);

		// Hint del buscador
		$('searchHint').textContent = 'La rifa juega con ' + r.number_length + ' dígitos y cada boleta tiene ' +
			r.numbers_per_ticket + (r.numbers_per_ticket === 1 ? ' oportunidad' : ' oportunidades') + ' de ganar';
	}

	function setSearchEnabled(on) {
		$('posSearch').disabled = !on;
		$('btnSearch').disabled = !on;
	}

	// ---------- 2. Buscar número ----------
	function doSearch() {
		if (!state.raffleId) {
			showToast('Primero elige la rifa', 'warning');
			return;
		}
		const numero = $('posSearch').value.trim();
		if (!numero) {
			showToast('Ingresa el número a buscar', 'warning');
			return;
		}

		$('btnSearch').disabled = true;
		$('searchResult').innerHTML = '<div class="text-muted small"><span class="spinner-border spinner-border-sm me-1"></span>Buscando...</div>';

		fetch('<?= base_url('ventas/buscar-numero') ?>?raffle_id=' + state.raffleId + '&numero=' + encodeURIComponent(numero))
			.then((r) => r.json())
			.then((data) => {
				$('btnSearch').disabled = false;
				if (!data.ok) {
					showToast(data.message, 'error');
					$('searchResult').innerHTML = '';
					return;
				}
				if (!data.found) {
					$('searchResult').innerHTML =
						'<div class="pos-num-card"><div class="text-muted"><i class="bx bx-error-circle me-1"></i>' +
						escapeHtml(data.message) + '</div></div>';
					return;
				}
				renderResult(data.ticket);
			})
			.catch(() => {
				$('btnSearch').disabled = false;
				$('searchResult').innerHTML = '';
				showToast('Error al buscar', 'error');
			});
	}

	$('btnSearch').addEventListener('click', doSearch);
	$('posSearch').addEventListener('keydown', function (e) {
		if (e.key === 'Enter') { e.preventDefault(); doSearch(); }
	});

	function renderResult(t) {
		const st = STATUS[t.status] || { label: t.status, cls: 'bg-secondary' };
		const inCart = state.cart.some((c) => c.id === t.id);
		const clase = t.status === 'AVAILABLE' ? 'is-available' : (t.status === 'SOLD' ? 'is-sold' : 'is-reserved');

		const numeros = t.numbers.map((n) =>
			'<span class="badge ' + (n === t.searched ? 'badge-num searched' : 'badge-num') + '">' + escapeHtml(n) + '</span>'
		).join('');

		let accion = '';
		if (t.status === 'AVAILABLE') {
			accion = inCart
				? '<button class="btn btn-sm btn-success" disabled><i class="bx bx-check me-1"></i>En el carrito</button>'
				: '<button class="btn btn-sm btn-primary" data-add="' + t.id + '"><i class="bx bx-cart-plus me-1"></i>Agregar</button>';
		} else {
			accion = '<span class="badge ' + st.cls + '">' + st.label + '</span>';
		}

		$('searchResult').innerHTML =
			'<div class="pos-num-card ' + clase + '">' +
				'<div class="pos-num-main">' +
					'<div>' +
						'<div class="pos-num-big">' + escapeHtml(t.searched) + '</div>' +
						'<div class="small text-muted">Boleta ' + escapeHtml(t.code) + ' · ' + money(t.price) + '</div>' +
					'</div>' +
					accion +
				'</div>' +
				(t.numbers.length > 1
					? '<div class="small text-muted mt-2 mb-1">Esta boleta incluye ' + t.numbers.length + ' oportunidades de ganar:</div>'
					: '') +
				'<div class="pos-numbers">' + numeros + '</div>' +
			'</div>';

		const btn = $('searchResult').querySelector('[data-add]');
		if (btn) {
			btn.addEventListener('click', function () {
				addToCart(t);
			});
		}
	}

	function resetSearch() {
		$('posSearch').value = '';
		$('searchResult').innerHTML = '';
	}

	// ---------- Carrito ----------
	function addToCart(t) {
		if (state.cart.some((c) => c.id === t.id)) return;
		state.cart.push({ id: t.id, code: t.code, price: t.price, numbers: t.numbers });
		renderCart();
		renderResult(t);
		showToast('Boleta ' + t.code + ' agregada al carrito', 'success');
	}

	function removeFromCart(id) {
		state.cart = state.cart.filter((c) => c.id !== id);
		renderCart();
		// refrescar el botón del resultado actual
		const btn = $('searchResult').querySelector('[data-add]');
		if (btn) {
			const tid = parseInt(btn.getAttribute('data-add'), 10);
			if (state.cart.some((c) => c.id === tid)) {
				btn.outerHTML = '<button class="btn btn-sm btn-success" disabled><i class="bx bx-check me-1"></i>En el carrito</button>';
			}
		}
	}

	function clearCart(silent) {
		state.cart = [];
		renderCart();
		if (!silent) showToast('Carrito vaciado', 'info');
	}

	function renderCart() {
		const n = state.cart.length;
		$('cartCount').textContent = n;
		$('cartEmpty').classList.toggle('d-none', n > 0);
		$('cartTotalWrap').classList.toggle('d-none', n === 0);

		$('cartItems').innerHTML = state.cart.map((c) =>
			'<div class="cart-item">' +
				'<div>' +
					'<div class="cart-nums">' + c.numbers.map(escapeHtml).join(' · ') + '</div>' +
					'<div class="cart-code">' + escapeHtml(c.code) + '</div>' +
				'</div>' +
				'<div class="d-flex align-items-center gap-2">' +
					'<span class="cart-price">' + money(c.price) + '</span>' +
					'<button type="button" class="btn-remove" data-remove="' + c.id + '" title="Quitar"><i class="bx bx-x"></i></button>' +
				'</div>' +
			'</div>'
		).join('');

		$('cartItems').querySelectorAll('[data-remove]').forEach((btn) => {
			btn.addEventListener('click', function () {
				removeFromCart(parseInt(this.getAttribute('data-remove'), 10));
			});
		});

		const total = state.cart.reduce((sum, c) => sum + Number(c.price), 0);
		$('cartTotal').textContent = money(total);
		updateConfirm();
	}

	// ---------- Pago ----------
	$('posMethod').addEventListener('change', function () {
		$('refGroup').classList.toggle('d-none', this.value !== 'TRANSFER');
		updateConfirm();
	});

	$('posParticipant').addEventListener('change', updateConfirm);

	function updateConfirm() {
		const ok = state.raffleId && state.cart.length > 0 && $('posParticipant').value !== '';
		$('btnConfirm').disabled = !ok;
	}

	$('btnConfirm').addEventListener('click', function () {
		if (!state.raffleId) { showToast('Elige la rifa', 'warning'); return; }
		if (state.cart.length === 0) { showToast('Agrega al menos una boleta', 'warning'); return; }
		if (!$('posParticipant').value) { showToast('Selecciona el participante', 'warning'); return; }

		const method = $('posMethod').value;
		const reference = $('posReference').value.trim();
		if (method === 'TRANSFER' && !reference) {
			showToast('La transferencia requiere la referencia', 'warning');
			$('posReference').focus();
			return;
		}

		const total = state.cart.reduce((sum, c) => sum + Number(c.price), 0);
		const label = method === 'CASH' ? 'Efectivo' : (method === 'TRANSFER' ? 'Transferencia' : 'Crédito (pagará después)');

		Swal.fire({
			title: '¿Confirmar venta?',
			html: '<div class="text-start small">' +
				'<b>Boletas:</b> ' + state.cart.length + '<br>' +
				'<b>Total:</b> ' + money(total) + '<br>' +
				'<b>Pago:</b> ' + label +
				'</div>',
			icon: 'question',
			showCancelButton: true,
			confirmButtonColor: '#198754',
			confirmButtonText: 'Sí, vender',
			cancelButtonText: 'Cancelar'
		}).then((result) => {
			if (!result.isConfirmed) return;

			$('fRaffle').value = state.raffleId;
			$('fParticipant').value = $('posParticipant').value;
			$('fMethod').value = method;
			$('fReference').value = reference;

			const cont = $('fTickets');
			cont.innerHTML = '';
			state.cart.forEach((c) => {
				const input = document.createElement('input');
				input.type = 'hidden';
				input.name = 'ticket_ids[]';
				input.value = c.id;
				cont.appendChild(input);
			});

			$('btnConfirm').disabled = true;
			$('btnConfirm').innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Procesando...';
			$('posForm').submit();
		});
	});

	// ---------- Participante rápido ----------
	$('formRapido').addEventListener('submit', function (e) {
		e.preventDefault();
		const form = this;
		const btn = $('btnRapidoGuardar');
		const errores = $('erroresRapido');
		errores.classList.add('d-none');

		btn.disabled = true;
		btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

		const data = new FormData(form);

		fetch(form.action, { method: 'POST', body: data })
			.then(async (r) => {
				const ctype = r.headers.get('content-type') || '';
				if (ctype.indexOf('application/json') !== -1) {
					return r.json();
				}
				throw new Error('Respuesta inesperada del servidor');
			})
			.then((json) => {
				btn.disabled = false;
				btn.innerHTML = '<i class="bx bx-save me-1"></i>Guardar';

				if (!json.ok) {
					errores.textContent = json.errors || 'No se pudo crear el participante.';
					errores.classList.remove('d-none');
					return;
				}

				const sel = $('posParticipant');
				const opt = document.createElement('option');
				opt.value = json.id;
				opt.textContent = json.label;
				opt.selected = true;
				sel.appendChild(opt);

				bootstrap.Modal.getOrCreateInstance($('modalParticipante')).hide();
				form.reset();
				updateConfirm();
				showToast('Participante creado', 'success');
			})
			.catch(() => {
				btn.disabled = false;
				btn.innerHTML = '<i class="bx bx-save me-1"></i>Guardar';
				errores.textContent = 'Error de conexión con el servidor.';
				errores.classList.remove('d-none');
			});
	});

	function escapeHtml(str) {
		return String(str)
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;');
	}
});
</script>
<?= $this->endSection() ?>
