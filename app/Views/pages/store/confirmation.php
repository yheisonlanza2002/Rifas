<?= $this->extend('layouts/store') ?>

<?= $this->section('styles') ?>
<style>
    @media print {
        body { background: #fff !important; }
        .store-navbar, .store-footer, .no-print { display: none !important; }
        .card-ticket { box-shadow: none !important; border: 1px solid #000 !important; }
    }
    .card-ticket {
        border-radius: 16px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.08);
        border: 2px dashed #cbd5e1;
        background: #ffffff;
        position: relative;
    }
    .ticket-badge {
        font-size: 1.35rem;
        font-weight: 800;
        letter-spacing: 2px;
        padding: 8px 16px;
        border-radius: 8px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container py-5">
    <div class="max-w-700 mx-auto" style="max-width: 650px;">

        <!-- ESTADO DE LA ORDEN -->
        <div class="text-center mb-4 no-print">
            <?php if ($sale['status'] === 'PAID'): ?>
                <div class="bg-success text-white rounded-circle d-inline-flex p-3 mb-3 shadow">
                    <i class='bx bx-check fs-1'></i>
                </div>
                <h3 class="fw-bold text-success mb-1">¡Felicidades, Pago Confirmado!</h3>
                <p class="text-secondary mb-0">Tus boletas han sido registradas y ya están participando en el sorteo.</p>
            <?php else: ?>
                <div class="bg-warning text-dark rounded-circle d-inline-flex p-3 mb-3 shadow">
                    <i class='bx bx-time fs-1'></i>
                </div>
                <h3 class="fw-bold text-dark mb-1">Orden Registrada — Pendiente de Pago</h3>
                <p class="text-secondary mb-0">Si pagaste por Wompi, el estado se actualizará en unos instantes. Si pagaste por transferencia, envía tu comprobante.</p>
            <?php endif; ?>
        </div>

        <!-- BOLETA DIGITAL IMPRIMIBLE -->
        <div class="card card-ticket p-4 p-md-5 mb-4">
            <!-- CABECERA DE LA BOLETA -->
            <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <img src="<?= base_url('assets/images/logo-icon.png') ?>" alt="Logo" width="36">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">BOLETA DIGITAL OFICIAL</h5>
                        <small class="text-muted">Orden #<?= esc($sale['sale_code']) ?></small>
                    </div>
                </div>
                <div>
                    <?php if ($sale['status'] === 'PAID'): ?>
                        <span class="badge bg-success px-3 py-2 rounded-pill fs-6 fw-bold">PAGADA</span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fs-6 fw-bold">PENDIENTE</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- DETALLE DE LA RIFA -->
            <div class="mb-4">
                <div class="text-muted small text-uppercase fw-semibold mb-1">Rifa</div>
                <h4 class="fw-bold text-primary mb-2"><?= esc($raffle['name'] ?? 'Rifa Oficial') ?></h4>
                <div class="d-flex flex-wrap gap-3 small text-secondary">
                    <?php if (!empty($raffle['end_at'])): ?>
                        <div><i class='bx bx-calendar text-primary me-1'></i> Juega el: <strong><?= date('d/m/Y', strtotime($raffle['end_at'])) ?></strong></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- NÚMEROS DE LA SUERTE ASIGNADOS -->
            <div class="p-3 bg-light rounded-3 border mb-4 text-center">
                <div class="text-muted small text-uppercase fw-bold mb-2">Tus números asignados</div>
                <div class="d-flex flex-wrap justify-content-center gap-2">
                    <?php foreach ($items as $it): ?>
                        <div class="p-2 border rounded bg-white shadow-sm">
                            <span class="badge bg-primary text-white ticket-badge">
                                <?= !empty($it['numbers']) ? implode(', ', $it['numbers']) : $it['code'] ?>
                            </span>
                            <div class="text-muted font-12 mt-1">Boleta #<?= esc($it['code']) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- DATOS DEL COMPRADOR -->
            <div class="row g-2 small text-secondary mb-3">
                <div class="col-6">
                    <span class="text-muted">Comprador:</span>
                    <strong class="text-dark d-block"><?= esc($participant['name'] . ' ' . ($participant['last_name'] ?? '')) ?></strong>
                </div>
                <div class="col-6 text-end">
                    <span class="text-muted">Teléfono / WhatsApp:</span>
                    <strong class="text-dark d-block"><?= esc($participant['phone'] ?? 'N/A') ?></strong>
                </div>
                <?php if (!empty($participant['document_number'])): ?>
                    <div class="col-6">
                        <span class="text-muted">Documento:</span>
                        <strong class="text-dark d-block"><?= esc(($participant['document_type'] ?? 'CC') . ' ' . $participant['document_number']) ?></strong>
                    </div>
                <?php endif; ?>
                <div class="col-6 text-end">
                    <span class="text-muted">Total Pagado:</span>
                    <strong class="text-success fs-6 d-block">$ <?= number_format((float) $sale['total'], 0, ',', '.') ?> <?= esc($sale['currency']) ?></strong>
                </div>
            </div>

            <div class="text-center pt-3 border-top small text-muted">
                Emitido el <?= date('d/m/Y h:i A', strtotime($sale['created_at'])) ?> — Conserve este comprobante para reclamar su premio en caso de resultar ganador.
            </div>
        </div>

        <!-- BOTONES DE ACCIÓN (NO IMPRIMIBLES) -->
        <div class="d-flex flex-column flex-sm-row gap-2 no-print">
            <button type="button" class="btn btn-outline-secondary flex-grow-1 py-2 fw-semibold rounded-pill" onclick="window.print()">
                <i class='bx bx-printer me-1'></i> Imprimir Boleta
            </button>

            <?php
            $shareText = urlencode(
                "¡Estoy participando en la rifa *" . ($raffle['name'] ?? 'Rifa') . "*!\n" .
                "Mis números de la suerte son: " . implode(', ', array_map(function($i) {
                    return !empty($i['numbers']) ? implode(', ', $i['numbers']) : $i['code'];
                }, $items)) . "\n" .
                "¡Comprueba el sorteo en: " . base_url('tienda/confirmacion/' . $sale['uuid']) . "!"
            );
            ?>
            <a href="https://wa.me/?text=<?= $shareText ?>" target="_blank" class="btn btn-success flex-grow-1 py-2 fw-semibold rounded-pill">
                <i class='bx bxl-whatsapp me-1'></i> Compartir en WhatsApp
            </a>

            <a href="<?= base_url('tienda') ?>" class="btn btn-primary flex-grow-1 py-2 fw-semibold rounded-pill">
                <i class='bx bx-store-alt me-1'></i> Ver Más Rifas
            </a>
        </div>

    </div>
</div>
<?= $this->endSection() ?>

