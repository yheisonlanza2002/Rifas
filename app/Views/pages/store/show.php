<?= $this->extend('layouts/store') ?>

<?= $this->section('styles') ?>
<style>
    .ticket-btn {
        width: 100%;
        padding: 10px 4px;
        font-weight: 700;
        font-size: 1.05rem;
        border: 2px solid #dee2e6;
        border-radius: 8px;
        background: #ffffff;
        color: #2b3445;
        transition: all 0.15s ease-in-out;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .ticket-btn:hover {
        border-color: #0d6efd;
        background: #f0f7ff;
        color: #0d6efd;
    }
    .ticket-btn.selected {
        border-color: #0d6efd;
        background: #0d6efd;
        color: #ffffff !important;
        box-shadow: 0 4px 10px rgba(13, 110, 253, 0.3);
    }
    .ticket-btn.selected .small-code {
        color: #dbeafe !important;
    }
    .ticket-btn .small-code {
        font-size: 0.7rem;
        color: #6c757d;
        font-weight: normal;
    }
    .ticket-btn.sold {
        background: #f8f9fa;
        color: #adb5bd;
        border-color: #e9ecef;
        cursor: not-allowed;
        text-decoration: line-through;
    }
    .cart-summary-card {
        position: sticky;
        top: 80px;
        border-radius: 14px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: 1px solid #e2e8f0;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container py-4">

    <!-- BREADCRUMB -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="<?= base_url('tienda') ?>">Rifas</a></li>
            <li class="breadcrumb-item active"><?= esc($raffle['name']) ?></li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- DETALLE DE LA RIFA Y SELECTOR DE BOLETAS -->
        <div class="col-lg-8">

            <!-- CARD DE INFORMACIÓN DE LA RIFA -->
            <div class="card card-raffle mb-4">
                <div class="row g-0">
                    <?php
                    $mainPrize = $prizes[0] ?? null;
                    $imgFile = !empty($mainPrize['prize_image']) ? $mainPrize['prize_image'] : (!empty($mainPrize['image']) ? $mainPrize['image'] : null);
                    $imgUrl = $imgFile ? base_url('uploads/prizes/' . $imgFile) : null;
                    ?>
                    <?php if ($imgUrl): ?>
                        <div class="col-md-5">
                            <img src="<?= esc($imgUrl) ?>" alt="<?= esc($raffle['name']) ?>" class="w-100 h-100 rounded-start" style="object-fit: cover; min-height: 220px;">
                        </div>
                    <?php endif; ?>
                    <div class="<?= $imgUrl ? 'col-md-7' : 'col-12' ?> p-4 d-flex flex-column justify-content-center">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-primary px-3 py-1 rounded-pill">Rifa Oficial</span>
                            <?php if ($lottery): ?>
                                <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-semibold">
                                    <i class='bx bx-dice-5 me-1'></i> <?= esc($lottery['name']) ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <h3 class="fw-bold text-dark mb-2"><?= esc($raffle['name']) ?></h3>

                        <?php if ($mainPrize): ?>
                            <h5 class="text-primary fw-semibold mb-2">
                                <i class='bx bx-trophy me-1'></i> <?= esc($mainPrize['name'] ?? $mainPrize['title'] ?? '') ?>
                                <?php if (!empty($mainPrize['value'])): ?>
                                    <span class="text-muted fs-6">($ <?= number_format((float) $mainPrize['value'], 0, ',', '.') ?>)</span>
                                <?php endif; ?>
                            </h5>
                        <?php endif; ?>

                        <?php if (!empty($raffle['description'])): ?>
                            <p class="text-secondary small mb-3"><?= nl2br(esc($raffle['description'])) ?></p>
                        <?php endif; ?>

                        <div class="d-flex flex-wrap gap-3 small text-muted pt-2 border-top">
                            <div><i class='bx bx-calendar text-primary me-1'></i> Sorteo: <strong><?= !empty($raffle['end_at']) ? date('d/m/Y', strtotime($raffle['end_at'])) : 'Por definir' ?></strong></div>
                            <div><i class='bx bx-purchase-tag text-success me-1'></i> Precio: <strong>$ <?= number_format((float) $raffle['ticket_price'], 0, ',', '.') ?> <?= esc($raffle['currency']) ?></strong></div>
                            <div><i class='bx bx-check-circle text-info me-1'></i> Disponibles: <strong><?= $stats['available'] ?></strong></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PREMIOS SECUNDARIOS SI EXISTEN -->
            <?php if (count($prizes) > 1): ?>
                <div class="card card-raffle mb-4 p-3">
                    <h6 class="fw-bold mb-3"><i class='bx bx-award text-warning me-1'></i>Todos los Premios de este Sorteo</h6>
                    <div class="row g-2">
                        <?php foreach ($prizes as $idx => $prz): ?>
                            <div class="col-md-6">
                                <div class="p-2 border rounded bg-light d-flex align-items-center gap-2">
                                    <div class="badge bg-secondary rounded-circle px-2 py-1"><?= $idx + 1 ?></div>
                                    <div>
                                        <div class="fw-semibold small"><?= esc($prz['name'] ?? $prz['title'] ?? '') ?></div>
                                        <?php if (!empty($prz['value'])): ?>
                                            <div class="text-muted font-12">$ <?= number_format((float) $prz['value'], 0, ',', '.') ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- SELECTOR INTERACTIVO DE NÚMEROS -->
            <div class="card card-raffle p-4">
                <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-3 pb-3 border-bottom">
                    <div>
                        <h5 class="fw-bold mb-1"><i class='bx bx-grid-alt text-primary me-2'></i>Elige tus Números</h5>
                        <p class="text-muted small mb-0">Haz clic en los números disponibles para agregarlos a tu carrito</p>
                    </div>

                    <!-- BOTONES DE SELECCIÓN RÁPIDA ALEATORIA -->
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-primary" onclick="seleccionarAleatorio(1)">🎲 1 al azar</button>
                        <button type="button" class="btn btn-outline-primary" onclick="seleccionarAleatorio(2)">🎲 2 al azar</button>
                        <button type="button" class="btn btn-outline-primary" onclick="seleccionarAleatorio(5)">🎲 5 al azar</button>
                    </div>
                </div>

                <!-- BUSCADOR DE NÚMERO ESPECÍFICO -->
                <div class="row g-2 mb-4">
                    <div class="col-sm-8">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class='bx bx-search'></i></span>
                            <input type="text" id="input-buscar-numero" class="form-control"
                                   placeholder="Busca tu número de la suerte (ej: 077)">
                            <button class="btn btn-primary px-3" type="button" onclick="buscarNumero()">
                                Buscar
                            </button>
                        </div>
                    </div>
                    <div class="col-sm-4 text-end">
                        <button class="btn btn-outline-secondary w-100" type="button" onclick="limpiarSeleccion()">
                            <i class='bx bx-trash me-1'></i> Limpiar Carrito
                        </button>
                    </div>
                </div>

                <!-- RESULTADO DE BÚSQUEDA DIRECTA -->
                <div id="resultado-busqueda" class="mb-3" style="display:none;"></div>

                <!-- GRID DE BOLETAS DISPONIBLES -->
                <div class="mb-2 text-muted small fw-semibold">
                    <i class='bx bx-check-double text-success me-1'></i>Boletas disponibles para elegir rápido:
                </div>

                <div class="row row-cols-3 row-cols-sm-4 row-cols-md-6 g-2" id="grid-boletas">
                    <?php if (empty($initialTickets)): ?>
                        <div class="col-12 text-center py-4 text-muted">
                            No hay boletas disponibles para mostrar en este momento.
                        </div>
                    <?php else: ?>
                        <?php foreach ($initialTickets as $t): ?>
                            <?php $numDisplay = !empty($t['numbers']) ? implode(', ', $t['numbers']) : $t['code']; ?>
                            <div class="col">
                                <button type="button"
                                        class="ticket-btn"
                                        id="btn-ticket-<?= $t['id'] ?>"
                                        data-ticket-id="<?= $t['id'] ?>"
                                        data-code="<?= esc($t['code']) ?>"
                                        data-numbers="<?= esc($numDisplay) ?>"
                                        data-price="<?= $t['price'] ?>"
                                        onclick="toggleTicket(this)">
                                    <span><?= esc($numDisplay) ?></span>
                                    <span class="small-code">#<?= esc($t['code']) ?></span>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="text-center mt-3 pt-3 border-top">
                    <small class="text-muted">¿Buscas un número en particular que no ves en la lista? Usa el buscador de arriba para encontrarlo.</small>
                </div>
            </div>

        </div>

        <!-- LATERAL: CARRITO Y FORMULARIO DE COMPRADOR -->
        <div class="col-lg-4">
            <div class="card card-raffle cart-summary-card">
                <div class="card-header bg-primary text-white py-3 rounded-top">
                    <h6 class="mb-0 fw-bold d-flex align-items-center justify-content-between">
                        <span><i class='bx bx-cart me-2'></i>Tu Carrito</span>
                        <span class="badge bg-white text-primary rounded-pill px-2" id="badge-contador">0 boletas</span>
                    </h6>
                </div>

                <div class="card-body p-4">
                    <form action="<?= base_url('tienda/checkout') ?>" method="POST" id="form-checkout">
                        <?= csrf_field() ?>
                        <input type="hidden" name="raffle_id" value="<?= esc($raffle['id']) ?>">
                        <div id="hidden-ticket-inputs"></div>

                        <!-- LISTA DE BOLETAS SELECCIONADAS -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-2">Boletas seleccionadas</label>
                            <div id="lista-carrito" class="bg-light p-2 rounded border" style="min-height: 80px; max-height: 150px; overflow-y: auto;">
                                <p class="text-muted small text-center my-3" id="msg-carrito-vacio">
                                    <i class='bx bx-pointer me-1'></i> Selecciona una o más boletas de la lista.
                                </p>
                            </div>
                        </div>

                        <!-- TOTAL -->
                        <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded border mb-4">
                            <span class="fw-bold text-dark">Total a pagar:</span>
                            <span class="fs-4 fw-bold text-primary" id="txt-total">$ 0</span>
                        </div>

                        <hr class="my-3">

                        <!-- DATOS DEL COMPRADOR -->
                        <h6 class="fw-bold text-dark mb-3"><i class='bx bx-user me-1'></i>Tus Datos Personales</h6>

                        <div class="mb-3">
                            <label class="form-label small">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" name="name" required placeholder="Tu nombre">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small">Apellidos</label>
                            <input type="text" class="form-control form-control-sm" name="last_name" placeholder="Tus apellidos">
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-4">
                                <label class="form-label small">Tipo Doc.</label>
                                <select class="form-select form-select-sm" name="document_type">
                                    <option value="CC">CC</option>
                                    <option value="CE">CE</option>
                                    <option value="NIT">NIT</option>
                                    <option value="PP">Pasaporte</option>
                                </select>
                            </div>
                            <div class="col-8">
                                <label class="form-label small">Número Doc.</label>
                                <input type="text" class="form-control form-control-sm" name="document_number" placeholder="Ej: 1020304050">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small">Teléfono / WhatsApp <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class='bx bxl-whatsapp text-success'></i></span>
                                <input type="tel" class="form-control" name="phone" required placeholder="Ej: 3001234567">
                            </div>
                            <div class="form-text font-12">Recibirás aquí tu comprobante oficial de compra.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small">Correo electrónico</label>
                            <input type="email" class="form-control form-control-sm" name="email" placeholder="correo@ejemplo.com">
                        </div>

                        <!-- MÉTODO DE PAGO PREFERIDO -->
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-2">Método de Pago</label>
                            <div class="d-flex flex-column gap-2">
                                <div class="form-check p-2 border rounded bg-white">
                                    <input class="form-check-input ms-1" type="radio" name="payment_driver" id="pay-wompi" value="wompi" checked>
                                    <label class="form-check-label ms-2 small fw-semibold" for="pay-wompi">
                                        <i class='bx bx-credit-card text-primary me-1'></i> Pago en Línea (Wompi)
                                        <span class="d-block text-muted font-12 fw-normal">Tarjetas, PSE, Nequi, Bancolombia</span>
                                    </label>
                                </div>
                                <div class="form-check p-2 border rounded bg-white">
                                    <input class="form-check-input ms-1" type="radio" name="payment_driver" id="pay-manual" value="manual">
                                    <label class="form-check-label ms-2 small fw-semibold" for="pay-manual">
                                        <i class='bx bx-transfer-alt text-success me-1'></i> Transferencia Manual
                                        <span class="d-block text-muted font-12 fw-normal">Consignación bancaria / WhatsApp</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <button type="submit" id="btn-submit-checkout" class="btn btn-primary w-100 py-3 fw-bold rounded-pill shadow-sm" disabled>
                            Proceder al Pago
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const ticketPrice = <?= (float) $raffle['ticket_price'] ?>;
    const currency = '<?= esc($raffle['currency']) ?>';
    let selectedTickets = new Map(); // id => { id, code, numbers, price }

    function formatNumber(num) {
        return new Intl.NumberFormat('es-CO').format(num);
    }

    function toggleTicket(btn) {
        const id = btn.getAttribute('data-ticket-id');
        const code = btn.getAttribute('data-code');
        const numbers = btn.getAttribute('data-numbers');
        const price = parseFloat(btn.getAttribute('data-price')) || ticketPrice;

        if (selectedTickets.has(id)) {
            selectedTickets.delete(id);
            btn.classList.remove('selected');
        } else {
            selectedTickets.set(id, { id, code, numbers, price });
            btn.classList.add('selected');
        }

        actualizarCarrito();
    }

    function removeTicket(id) {
        selectedTickets.delete(id);
        const btn = document.getElementById('btn-ticket-' + id);
        if (btn) btn.classList.remove('selected');
        actualizarCarrito();
    }

    function actualizarCarrito() {
        const lista = document.getElementById('lista-carrito');
        const hiddenInputs = document.getElementById('hidden-ticket-inputs');
        const badgeContador = document.getElementById('badge-contador');
        const txtTotal = document.getElementById('txt-total');
        const btnSubmit = document.getElementById('btn-submit-checkout');

        hiddenInputs.innerHTML = '';
        lista.innerHTML = '';

        if (selectedTickets.size === 0) {
            lista.innerHTML = `
                <p class="text-muted small text-center my-3" id="msg-carrito-vacio">
                    <i class='bx bx-pointer me-1'></i> Selecciona una o más boletas de la lista.
                </p>
            `;
            badgeContador.textContent = '0 boletas';
            txtTotal.textContent = '$ 0';
            btnSubmit.disabled = true;
            btnSubmit.textContent = 'Proceder al Pago';
            return;
        }

        let total = 0;
        selectedTickets.forEach(t => {
            total += t.price;

            // Input oculto para POST
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ticket_ids[]';
            input.value = t.id;
            hiddenInputs.appendChild(input);

            // Item visual en carrito
            const item = document.createElement('div');
            item.className = 'd-flex align-items-center justify-content-between p-2 mb-1 bg-white rounded border small';
            item.innerHTML = `
                <div>
                    <span class="fw-bold text-dark">${t.numbers}</span>
                    <span class="text-muted font-12 ms-1">#${t.code}</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-semibold text-primary">$ ${formatNumber(t.price)}</span>
                    <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="removeTicket('${t.id}')">
                        <i class='bx bx-x fs-5'></i>
                    </button>
                </div>
            `;
            lista.appendChild(item);
        });

        badgeContador.textContent = `${selectedTickets.size} boleta${selectedTickets.size > 1 ? 's' : ''}`;
        txtTotal.textContent = `$ ${formatNumber(total)} ${currency}`;
        btnSubmit.disabled = false;
        btnSubmit.textContent = `Proceder al Pago ($ ${formatNumber(total)})`;
    }

    function limpiarSeleccion() {
        selectedTickets.clear();
        document.querySelectorAll('.ticket-btn.selected').forEach(btn => btn.classList.remove('selected'));
        actualizarCarrito();
    }

    function seleccionarAleatorio(cantidad) {
        fetch(`<?= base_url('tienda/buscar-numero') ?>?raffle_id=<?= $raffle['id'] ?>&random=${cantidad}`)
            .then(res => res.json())
            .then(data => {
                if (!data.ok || !data.tickets || data.tickets.length === 0) {
                    Swal.fire({ icon: 'info', title: 'Sin boletas', text: data.message || 'No hay suficientes boletas disponibles.' });
                    return;
                }

                data.tickets.forEach(t => {
                    const numDisplay = (t.numbers && t.numbers.length > 0) ? t.numbers.join(', ') : t.code;
                    const idStr = String(t.id);
                    selectedTickets.set(idStr, {
                        id: t.id,
                        code: t.code,
                        numbers: numDisplay,
                        price: parseFloat(t.price) || ticketPrice
                    });

                    // Si existe en el grid, seleccionarlo visualmente
                    const btn = document.getElementById('btn-ticket-' + t.id);
                    if (btn) btn.classList.add('selected');
                });

                actualizarCarrito();
                Swal.fire({
                    icon: 'success',
                    title: '¡Boletas agregadas!',
                    text: `Se agregaron ${data.tickets.length} boleta(s) aleatoria(s) a tu carrito.`,
                    timer: 1800,
                    showConfirmButton: false
                });
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo conectar con el servidor.' });
            });
    }

    function buscarNumero() {
        const query = document.getElementById('input-buscar-numero').value.trim();
        const resDiv = document.getElementById('resultado-busqueda');

        if (!query) return;

        fetch(`<?= base_url('tienda/buscar-numero') ?>?raffle_id=<?= $raffle['id'] ?>&q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                resDiv.style.display = 'block';

                if (!data.ok || !data.found) {
                    resDiv.innerHTML = `
                        <div class="alert alert-warning py-2 small d-flex align-items-center gap-2">
                            <i class='bx bx-info-circle fs-5'></i>
                            <div>${data.message || 'El número no fue encontrado.'}</div>
                        </div>
                    `;
                    return;
                }

                const t = data.ticket;
                if (t.status !== 'AVAILABLE') {
                    resDiv.innerHTML = `
                        <div class="alert alert-danger py-2 small d-flex align-items-center gap-2">
                            <i class='bx bx-x-circle fs-5'></i>
                            <div>El número <strong>${query}</strong> (Boleta #${t.code}) ya se encuentra <strong>VENDIDO O RESERVADO</strong>.</div>
                        </div>
                    `;
                    return;
                }

                const numDisplay = (t.numbers && t.numbers.length > 0) ? t.numbers.join(', ') : t.code;
                resDiv.innerHTML = `
                    <div class="alert alert-success py-2 small d-flex align-items-center justify-content-between">
                        <div>
                            <i class='bx bx-check-circle me-1'></i>
                            ¡Número disponible! <strong>${numDisplay}</strong> (#${t.code}) - $ ${formatNumber(t.price)}
                        </div>
                        <button type="button" class="btn btn-sm btn-success px-3" onclick="agregarDesdeBusqueda(${t.id}, '${t.code}', '${numDisplay}', ${t.price})">
                            <i class='bx bx-plus me-1'></i> Agregar
                        </button>
                    </div>
                `;
            })
            .catch(err => {
                resDiv.style.display = 'block';
                resDiv.innerHTML = `<div class="alert alert-danger py-2 small">Error de conexión.</div>`;
            });
    }

    function agregarDesdeBusqueda(id, code, numbers, price) {
        selectedTickets.set(String(id), { id, code, numbers, price });
        const btn = document.getElementById('btn-ticket-' + id);
        if (btn) btn.classList.add('selected');
        actualizarCarrito();
        document.getElementById('resultado-busqueda').style.display = 'none';
        document.getElementById('input-buscar-numero').value = '';
    }

    // Permitir buscar con tecla Enter
    document.getElementById('input-buscar-numero').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            buscarNumero();
        }
    });
</script>
<?= $this->endSection() ?>

