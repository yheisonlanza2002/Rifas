<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Roles y Permisos</h4>
        <p class="text-muted mb-0">Define qué puede hacer cada rol dentro del sistema.</p>
    </div>
    <a href="<?= base_url('roles/crear') ?>" class="btn btn-primary">
        <i class="bx bx-plus"></i> Nuevo Rol
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Rol</th>
                        <th>Descripción</th>
                        <th class="text-center">Permisos</th>
                        <th class="text-center">Sistema</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($roles)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="bx bx-info-circle fs-4 d-block mb-2"></i>
                                No hay roles registrados.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($roles as $r): ?>
                            <tr>
                                <td><b><?= esc($r['name']) ?></b></td>
                                <td><?= esc($r['description'] ?? '—') ?></td>
                                <td class="text-center">
                                    <span class="badge bg-primary-subtle text-primary"><?= (int) $r['total_permisos'] ?></span>
                                </td>
                                <td class="text-center">
                                    <?php if ($r['is_system']): ?>
                                        <span class="badge bg-secondary">Sistema</span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-dark">Normal</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <a href="<?= base_url('roles/permisos/' . $r['id']) ?>" class="btn btn-sm btn-primary">
                                        <i class="bx bx-key"></i> Administrar permisos
                                    </a>

                                    <?php if (!$r['is_system']): ?>
                                        <a href="<?= base_url('roles/' . $r['id'] . '/eliminar') ?>"
                                           class="btn btn-sm btn-outline-danger"
                                           data-confirm="¿Eliminar el rol <?= esc($r['name']) ?>?"
                                           data-confirm-text="Esta acción no se puede deshacer"
                                           data-confirm-btn="Sí, eliminar">
                                            <i class="bx bx-trash"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
