<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Premios</h4>
        <p class="text-muted mb-0">Gestiona los premios de las rifas.</p>
    </div>
    <a href="<?= base_url('premios/crear') ?>" class="btn btn-primary">
        <i class="bx bx-plus"></i> Nuevo Premio
    </a>
</div>

<div class="row g-3">
    <?php if (empty($prizes)): ?>
        <div class="col-12">
            <div class="text-center py-5 text-muted">
                <i class="bx bx-info-circle fs-1 d-block mb-3"></i>
                <p>No hay premios registrados.</p>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($prizes as $p): ?>
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="prize-card">
                    <div class="prize-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold"><?= esc($p['name']) ?></span>
                            <span class="badge <?= $p['status'] === 'ACTIVE' ? 'bg-success' : 'bg-secondary' ?>">
                                <?= $p['status'] === 'ACTIVE' ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </div>
                    </div>
                    <div class="prize-body">
                        <p class="text-muted small mb-3"><?= esc($p['description'] ?? 'Sin descripción') ?></p>
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            <span class="badge bg-primary-subtle text-primary">
                                <i class="bx bx-trophy"></i> <?= esc($p['raffle_name'] ?? '—') ?>
                            </span>
                            <span class="badge bg-info-subtle text-info">
                                <i class="bx bx-building"></i> <?= esc($p['lottery_name'] ?? '—') ?>
                            </span>
                        </div>
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            <?php if ($p['prize_type'] === 'MONEY'): ?>
                                <span class="badge bg-success-subtle text-success">
                                    <i class="bx bx-money"></i> Dinero
                                </span>
                                <?php if ($p['recurrence_days']): ?>
                                    <span class="badge bg-warning-subtle text-warning">
                                        Cada <?= $p['recurrence_days'] ?> días
                                    </span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="badge bg-purple-subtle text-purple">
                                    <i class="bx bx-gift"></i> Físico
                                </span>
                            <?php endif; ?>
                            <span class="badge bg-light text-dark">
                                <i class="bx bx-hash"></i> #<?= esc($p['position']) ?>
                            </span>
                        </div>
                        <div class="mt-3">
                            <span class="badge bg-dark text-light">
                                $ <?= number_format($p['value'], 0) ?> <?= esc($p['currency']) ?>
                            </span>
                        </div>
                    </div>
                    <div class="prize-footer">
                        <div class="d-flex gap-2">
                            <a href="<?= base_url('premios/' . $p['id'] . '/editar') ?>"
                               class="btn btn-sm btn-outline-primary flex-fill">
                                <i class="bx bx-edit-alt"></i> Editar
                            </a>
                            <a href="<?= base_url('premios/' . $p['id'] . '/eliminar') ?>"
                               class="btn btn-sm btn-outline-danger"
                               data-confirm="¿Eliminar el premio <?= esc($p['name']) ?>?"
                               data-confirm-text="Esta acción no se puede deshacer"
                               data-confirm-btn="Sí, eliminar">
                                <i class="bx bx-trash"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
