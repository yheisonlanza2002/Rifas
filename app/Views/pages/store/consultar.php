<?= $this->extend('layouts/store') ?>

<?= $this->section('content') ?>
<div class="container py-5">
    <div class="max-w-700 mx-auto" style="max-width: 720px;">

        <!-- BUSCADOR -->
        <div class="card card-raffle mb-4 p-4 text-center">
            <h4 class="fw-bold mb-2 text-dark"><i class='bx bx-search-alt text-primary me-2'></i>Consultar Mis Boletas</h4>
            <p class="text-secondary small mb-4">Ingresa tu número de teléfono móvil o documento de identidad con el que realizaste la compra.</p>

            <form action="<?= base_url('tienda/consultar') ?>" method="GET" class="row g-2 justify-content-center">
                <div class="col-sm-8">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class='bx bx-user'></i></span>
                        <input type="text" name="q" class="form-control form-control-lg fs-6"
                               placeholder="Ej: 3001234567 o 10203040"
                               value="<?= esc($q ?? '') ?>" required>
                    </div>
                </div>
                <div class="col-sm-3">
                    <button type="submit" class="btn btn-primary btn-lg w-100 fs-6 rounded-pill">
                        Buscar
                    </button>
                </div>
            </form>
        </div>

        <!-- RESULTADOS -->
        <?php if ($searched): ?>
            <?php if (empty($sales)): ?>
                <div class="alert alert-warning text-center py-4 rounded-3">
                    <i class='bx bx-info-circle fs-3 d-block mb-2'></i>
                    <h6 class="fw-bold mb-1">No se encontraron compras</h6>
                    <p class="mb-0 small">No hay boletas registradas con el número <strong><?= esc($q) ?></strong>. Verifica que esté bien escrito o comunícate con nosotros.</p>
                </div>
            <?php else: ?>
                <h5 class="fw-bold mb-3 text-dark"><i class='bx bx-list-check me-2 text-success'></i>Compras Encontradas (<?= count($sales) ?>)</h5>

                <div class="d-flex flex-column gap-3">
                    <?php foreach ($sales as $s): ?>
                        <div class="card card-raffle p-4">
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center pb-2 mb-3 border-bottom gap-2">
                                <div>
                                    <span class="badge bg-light text-dark border me-1">#<?= esc($s['sale_code']) ?></span>
                                    <span class="small text-muted"><?= date('d/m/Y h:i A', strtotime($s['created_at'])) ?></span>
                                </div>
                                <div>
                                    <?php if ($s['status'] === 'PAID'): ?>
                                        <span class="badge bg-success px-3 py-1 rounded-pill">PAGADA</span>
                                    <?php elseif ($s['status'] === 'PENDING'): ?>
                                        <span class="badge bg-warning text-dark px-3 py-1 rounded-pill">PENDIENTE</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary px-3 py-1 rounded-pill"><?= esc($s['status']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small mb-1">Boletas y números asignados:</div>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php if (!empty($s['tickets'])): ?>
                                        <?php foreach ($s['tickets'] as $tk): ?>
                                            <?php $numDisplay = !empty($tk['numbers']) ? implode(', ', $tk['numbers']) : $tk['code']; ?>
                                            <span class="badge bg-primary fs-6 px-3 py-2 rounded">
                                                <?= esc($numDisplay) ?> <small class="opacity-75 font-11">(#<?= esc($tk['code']) ?>)</small>
                                            </span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="text-muted small">Sin boletas asociadas</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                                <span class="fw-bold text-dark">Total: $ <?= number_format((float) $s['total'], 0, ',', '.') ?> <?= esc($s['currency']) ?></span>
                                <a href="<?= base_url('tienda/confirmacion/' . $s['uuid']) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                    <i class='bx bx-show me-1'></i> Ver Comprobante
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

    </div>
</div>
<?= $this->endSection() ?>

