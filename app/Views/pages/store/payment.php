<?= $this->extend('layouts/store') ?>

<?= $this->section('content') ?>
<div class="container py-5">
    <div class="max-w-700 mx-auto" style="max-width: 680px;">

        <!-- TARJETA PRINCIPAL DE PAGO -->
        <div class="card card-raffle border-0 shadow-lg overflow-hidden">
            <div class="card-header bg-gradient-cosmic text-white p-4 text-center">
                <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-semibold mb-2">Orden de Compra</span>
                <h4 class="fw-bold mb-1">Finaliza tu Pago</h4>
                <p class="mb-0 small text-white-50">Código de Orden: <strong><?= esc($sale['sale_code']) ?></strong></p>
            </div>

            <div class="card-body p-4 p-md-5">

                <!-- RESUMEN DE COMPRA -->
                <div class="p-3 bg-light rounded-3 border mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">Rifa:</span>
                        <span class="fw-bold text-dark"><?= esc($raffle['name'] ?? 'Rifa') ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">Participante:</span>
                        <span class="fw-semibold text-dark"><?= esc($participant['name'] . ' ' . ($participant['last_name'] ?? '')) ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">WhatsApp:</span>
                        <span class="text-dark"><?= esc($participant['phone'] ?? '') ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small">Boletas (<?= count($items) ?>):</span>
                        <div class="text-end">
                            <?php foreach ($items as $it): ?>
                                <span class="badge bg-primary me-1 mb-1 font-13">
                                    <?= !empty($it['numbers']) ? implode(', ', $it['numbers']) : $it['code'] ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <span class="fs-5 fw-bold text-dark">Total a Pagar:</span>
                        <span class="fs-3 fw-bold text-success">$ <?= number_format((float) $sale['total'], 0, ',', '.') ?> <?= esc($sale['currency']) ?></span>
                    </div>
                </div>

                <!-- ALERTA DE RESERVA TEMPORAL -->
                <div class="alert alert-info py-2 small d-flex align-items-center gap-2 mb-4">
                    <i class='bx bx-time-five fs-4'></i>
                    <div>Tus boletas se encuentran <strong>reservadas temporalmente</strong>. Realiza el pago para confirmarlas.</div>
                </div>

                <!-- OPCIÓN 1: PAGO DIGITAL CON WOMPI -->
                <?php if ($wompiGateway && !empty($wompiConfig['pub_key'])): ?>
                    <div class="card border-warning border-2 mb-4">
                        <div class="card-body p-4 text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                                <h5 class="fw-bold mb-0 text-dark">Pago en Línea con Wompi</h5>
                                <?php if (!empty($wompiGateway['test_mode'])): ?>
                                    <span class="badge bg-warning text-dark small">Sandbox</span>
                                <?php endif; ?>
                            </div>
                            <p class="small text-muted mb-4">Paga al instante con Tarjetas de Crédito/Débito, PSE, Nequi o Botón Bancolombia.</p>

                            <!-- FORMULARIO OFICIAL CHECKOUT WOMPI -->
                            <form action="https://checkout.wompi.co/p/" method="GET">
                                <input type="hidden" name="public-key" value="<?= esc($wompiConfig['pub_key']) ?>" />
                                <input type="hidden" name="currency" value="<?= esc($sale['currency'] ?? 'COP') ?>" />
                                <input type="hidden" name="amount-in-cents" value="<?= (int) round(((float) $sale['total']) * 100) ?>" />
                                <input type="hidden" name="reference" value="<?= esc($sale['sale_code']) ?>" />

                                <?php if (!empty($wompiSignature)): ?>
                                    <input type="hidden" name="signature:integrity" value="<?= esc($wompiSignature) ?>" />
                                <?php endif; ?>

                                <input type="hidden" name="redirect-url" value="<?= base_url('tienda/confirmacion/' . $sale['uuid']) ?>" />
                                <input type="hidden" name="customer-data:email" value="<?= esc($participant['email'] ?? '') ?>" />
                                <input type="hidden" name="customer-data:full-name" value="<?= esc($participant['name'] . ' ' . ($participant['last_name'] ?? '')) ?>" />
                                <input type="hidden" name="customer-data:phone-number" value="<?= esc($participant['phone'] ?? '') ?>" />

                                <button type="submit" class="btn btn-warning w-100 py-3 fw-bold fs-5 rounded-pill shadow">
                                    <i class='bx bx-credit-card me-2'></i> Pagar Ahora con Wompi
                                </button>
                            </form>
                            <small class="text-muted d-block mt-2 font-12"><i class='bx bx-shield-quarter text-success'></i> Transacción protegida por Wompi Bancolombia</small>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- OPCIÓN 2: PAGO MANUAL / TRANSFERENCIA -->
                <?php if ($manualGateway): ?>
                    <div class="card border-success border-2 mb-4">
                        <div class="card-header bg-success bg-opacity-10 py-3">
                            <h6 class="mb-0 fw-bold text-success text-center">
                                <i class='bx bx-money me-1'></i> Transferencia Manual / Consignación
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-2 mb-3 small">
                                <?php if (!empty($manualConfig['banco'])): ?>
                                    <div class="col-6 text-muted">Banco / Entidad:</div>
                                    <div class="col-6 fw-bold text-dark text-end"><?= esc($manualConfig['banco']) ?></div>
                                <?php endif; ?>

                                <?php if (!empty($manualConfig['tipo_cuenta'])): ?>
                                    <div class="col-6 text-muted">Tipo de Cuenta:</div>
                                    <div class="col-6 fw-bold text-dark text-end text-capitalize"><?= esc($manualConfig['tipo_cuenta']) ?></div>
                                <?php endif; ?>

                                <?php if (!empty($manualConfig['numero_cuenta'])): ?>
                                    <div class="col-6 text-muted">Número de Cuenta:</div>
                                    <div class="col-6 fw-bold text-primary text-end fs-6">
                                        <?= esc($manualConfig['numero_cuenta']) ?>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($manualConfig['titular'])): ?>
                                    <div class="col-6 text-muted">Titular:</div>
                                    <div class="col-6 fw-semibold text-dark text-end"><?= esc($manualConfig['titular']) ?></div>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($manualConfig['instrucciones'])): ?>
                                <div class="p-3 bg-light rounded small text-secondary mb-3">
                                    <strong>Instrucciones:</strong><br>
                                    <?= nl2br(esc($manualConfig['instrucciones'])) ?>
                                </div>
                            <?php endif; ?>

                            <?php
                            $msgWhatsApp = urlencode(
                                "¡Hola! Realicé el pago de mi orden en la tienda de Rifas.\n\n" .
                                "*Código de Orden:* " . $sale['sale_code'] . "\n" .
                                "*Rifa:* " . ($raffle['name'] ?? 'Rifa') . "\n" .
                                "*Monto:* $" . number_format((float) $sale['total'], 0, ',', '.') . " " . $sale['currency'] . "\n" .
                                "*Nombre:* " . $participant['name'] . "\n\n" .
                                "Adjunto mi comprobante para que activen mis boletas."
                            );
                            ?>

                            <a href="https://wa.me/?text=<?= $msgWhatsApp ?>" target="_blank"
                               class="btn btn-success w-100 py-3 fw-bold rounded-pill">
                                <i class='bx bxl-whatsapp me-2 fs-5'></i> Enviar Comprobante por WhatsApp
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="text-center mt-3">
                    <a href="<?= base_url('tienda/confirmacion/' . $sale['uuid']) ?>" class="btn btn-link text-decoration-none text-muted small">
                        Ya realicé el pago, ver mi comprobante →
                    </a>
                </div>

            </div>
        </div>

    </div>
</div>
<?= $this->endSection() ?>

