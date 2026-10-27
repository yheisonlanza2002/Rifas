<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
.perm-card {
    border: 1px solid #e6e8f0;
    border-radius: 12px;
    transition: all 0.2s ease;
    height: 100%;
}
.perm-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    transform: translateY(-2px);
}
.perm-card .perm-header {
    padding: 14px 16px;
    background: #f8f9fc;
    border-bottom: 1px solid #e6e8f0;
    border-radius: 12px 12px 0 0;
}
.perm-card .perm-body {
    padding: 16px;
}
.perm-card .perm-footer {
    padding: 12px 16px;
    background: #f8f9fc;
    border-top: 1px solid #e6e8f0;
    border-radius: 0 0 12px 12px;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Permisos</h4>
        <p class="text-muted mb-0">Gestiona los permisos del sistema.</p>
    </div>
    <a href="<?= base_url('permisos/crear') ?>" class="btn btn-primary">
        <i class="bx bx-plus"></i> Nuevo Permiso
    </a>
</div>

<div class="row g-3">
    <?php if (empty($permissions)): ?>
        <div class="col-12">
            <div class="text-center py-5 text-muted">
                <i class="bx bx-info-circle fs-1 d-block mb-3"></i>
                <p>No hay permisos registrados.</p>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($permissions as $p): ?>
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="perm-card">
                    <div class="perm-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold"><?= esc($p['name']) ?></span>
                            <span class="badge <?= $p['status'] === 'ACTIVE' ? 'bg-success' : 'bg-secondary' ?>">
                                <?= $p['status'] === 'ACTIVE' ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </div>
                    </div>
                    <div class="perm-body">
                        <p class="text-muted small mb-3"><?= esc($p['description'] ?? 'Sin descripción') ?></p>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-primary-subtle text-primary">
                                <i class="bx bx-folder"></i> <?= esc($p['module']) ?>
                            </span>
                            <span class="badge bg-info-subtle text-info">
                                <i class="bx bx-key"></i> <?= esc($p['action']) ?>
                            </span>
                            <span class="badge bg-light text-dark">
                                <i class="bx bx-shield"></i> <?= esc($p['guard_name']) ?>
                            </span>
                        </div>
                        <div class="mt-3">
                            <span class="badge bg-warning-subtle text-warning">
                                <i class="bx bx-user-check"></i> <?= $p['total_roles'] ?> roles
                            </span>
                        </div>
                    </div>
                    <div class="perm-footer">
                        <div class="d-flex gap-2">
                            <a href="<?= base_url('permisos/' . $p['id'] . '/editar') ?>"
                               class="btn btn-sm btn-outline-primary flex-fill">
                                <i class="bx bx-edit-alt"></i> Editar
                            </a>
                            <a href="<?= base_url('permisos/' . $p['id'] . '/eliminar') ?>"
                               class="btn btn-sm btn-outline-danger"
                               data-confirm="¿Eliminar el permiso <?= esc($p['name']) ?>?"
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
