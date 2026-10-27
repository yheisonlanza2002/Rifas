<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
.perm-group {
    border: 1px solid #e6e8f0;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.2s ease;
    background: #fff;
    position: relative;
}
.perm-group:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    border-color: #0d6efd;
}
.perm-group .perm-group-header {
    padding: 14px 16px;
    background: linear-gradient(135deg, #f8f9fc 0%, #e9ecef 100%);
    border-bottom: 1px solid #e6e8f0;
    font-weight: 700;
    font-size: 13.5px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    user-select: none;
    transition: background 0.2s ease;
}
.perm-group .perm-group-header:hover {
    background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
}
.perm-group .perm-group-header .badge {
    font-size: 11px;
    font-weight: 600;
    padding: 4px 8px;
}
.perm-group .perm-group-header .toggle-icon {
    transition: transform 0.3s ease;
    font-size: 18px;
    color: #0d6efd;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: rgba(13, 110, 253, 0.1);
}
.perm-group.cerrado .perm-group-header .toggle-icon {
    transform: rotate(-90deg);
    color: #6c757d;
    background: rgba(108, 117, 125, 0.1);
}
.perm-group .perm-group-body {
    padding: 12px;
    display: block;
    animation: slideDown 0.3s ease;
}
.perm-group.cerrado .perm-group-body {
    display: none;
}
@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.perm-check {
    cursor: pointer;
    user-select: none;
    transition: all 0.15s ease;
    border-radius: 8px;
    padding: 10px 12px;
    margin-bottom: 4px;
    position: relative;
    display: flex;
    align-items: center;
    gap: 10px;
    border: 1px solid transparent;
}
.perm-check:hover {
    background: #f8f9fc;
    border-color: #e6e8f0;
}
.perm-check input[type="checkbox"] {
    cursor: pointer;
    width: 16px;
    height: 16px;
}
.perm-check .perm-tooltip {
    display: none;
    position: absolute;
    left: 100%;
    top: 0;
    background: #343a40;
    color: #fff;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 12px;
    white-space: nowrap;
    z-index: 1000;
    margin-left: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    line-height: 1.5;
}
.perm-check:hover .perm-tooltip {
    display: block;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Permisos de <?= esc($role['name']) ?></h4>
        <p class="text-muted mb-0">Marca los permisos que se otorgarán a este rol.</p>
    </div>
    <a href="<?= base_url('roles') ?>" class="btn btn-outline-secondary">
        <i class="bx bx-arrow-back"></i> Volver
    </a>
</div>

<form action="<?= base_url('roles/actualizar-permisos/' . $role['id']) ?>" method="POST" autocomplete="off">
    <?= csrf_field() ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="mb-0">Permisos del rol</h6>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="marcarTodo(true)">
                <i class="bx bx-check-double"></i> Marcar todo
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="marcarTodo(false)">
                <i class="bx bx-x"></i> Desmarcar todo
            </button>
            <button type="button" class="btn btn-sm btn-outline-info" onclick="toggleTodos(true)">
                <i class="bx bx-chevron-down"></i> Abrir todos
            </button>
            <button type="button" class="btn btn-sm btn-outline-warning" onclick="toggleTodos(false)">
                <i class="bx bx-chevron-up"></i> Cerrar todos
            </button>
        </div>
    </div>

    <div class="row g-3">
        <?php foreach ($permisosAgrupados as $modulo => $items): ?>
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="perm-group cerrado">
                    <div class="perm-group-header" onclick="toggleGroup(event, this)">
                        <span>
                            <i class="bx bx-folder"></i>
                            <?= esc(ucfirst($modulo)) ?>
                        </span>
                        <div class="d-flex gap-1 align-items-center">
                            <span class="badge bg-light text-dark"><?= count($items) ?></span>
                            <span class="toggle-icon"><i class="bx bx-chevron-down"></i></span>
                        </div>
                    </div>
                    <div class="perm-group-body">
                        <?php foreach ($items as $p): ?>
                            <?php
                            $marcado = in_array($p['id'], $permisosDelRol) ? 'checked' : '';
                            ?>
                            <label class="perm-check">
                                <input class="form-check-input m-0" type="checkbox" name="permisos[]"
                                       value="<?= $p['id'] ?>" <?= $marcado ?>>
                                <span class="small"><?= esc($p['name']) ?></span>
                                <span class="perm-tooltip">
                                    <strong><?= esc($p['name']) ?></strong><br>
                                    Módulo: <?= esc($p['module']) ?><br>
                                    Acción: <?= esc($p['action']) ?><br>
                                    <?= esc($p['description'] ?? 'Sin descripción') ?>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="mt-4 d-flex gap-2">
        <button type="submit" class="btn btn-primary px-4">
            <i class="bx bx-save"></i> Guardar permisos
        </button>
        <a href="<?= base_url('roles') ?>" class="btn btn-outline-secondary px-4">Cancelar</a>
    </div>
</form>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function marcarTodo(on) {
    document.querySelectorAll('.perm-check input[type=checkbox]').forEach(function(cb) {
        cb.checked = on;
    });
}

function toggleGroup(event, header) {
    event.stopPropagation();
    const group = header.closest('.perm-group');
    group.classList.toggle('cerrado');
}

function toggleTodos(abrir) {
    document.querySelectorAll('.perm-group').forEach(function(group) {
        if (abrir) {
            group.classList.remove('cerrado');
        } else {
            group.classList.add('cerrado');
        }
    });
}

document.querySelectorAll('.perm-group').forEach(function(group) {
    group.addEventListener('mouseenter', function() {
        this.classList.remove('cerrado');
    });
});
</script>
<?= $this->endSection() ?>
