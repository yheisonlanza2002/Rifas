<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
.perm-group {
    border: 1px solid #e6e8f0;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.2s ease;
    background: #fff;
}
.perm-group:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
.perm-group .perm-group-header {
    padding: 12px 16px;
    background: #f8f9fc;
    border-bottom: 1px solid #e6e8f0;
    font-weight: 700;
    font-size: 13.5px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    user-select: none;
}
.perm-group .perm-group-header:hover {
    background: #e9ecef;
}
.perm-group .perm-group-header .badge {
    font-size: 11px;
    font-weight: 600;
}
.perm-group .perm-group-header .toggle-icon {
    transition: transform 0.2s ease;
    font-size: 16px;
    color: #6c757d;
}
.perm-group.cerrado .perm-group-header .toggle-icon {
    transform: rotate(-90deg);
}
.perm-group .perm-group-body {
    padding: 12px;
    display: block;
}
.perm-group.cerrado .perm-group-body {
    display: none;
}
.perm-check {
    cursor: pointer;
    user-select: none;
    transition: background 0.15s ease;
    border-radius: 6px;
    padding: 8px 10px;
    margin-bottom: 4px;
    position: relative;
}
.perm-check:hover {
    background: #f8f9fc;
}
.perm-check input[type="checkbox"] {
    cursor: pointer;
}
.perm-check .perm-tooltip {
    display: none;
    position: absolute;
    left: 100%;
    top: 0;
    background: #343a40;
    color: #fff;
    padding: 6px 10px;
    border-radius: 6px;
    font-size: 12px;
    white-space: nowrap;
    z-index: 1000;
    margin-left: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}
.perm-check:hover .perm-tooltip {
    display: block;
}
.module-toggle {
    font-size: 11px;
    padding: 2px 8px;
    border-radius: 4px;
    border: 1px solid #dee2e6;
    background: #fff;
    cursor: pointer;
    transition: all 0.15s ease;
}
.module-toggle:hover {
    background: #e9ecef;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Editar Rol: <?= esc($role['name']) ?></h4>
        <p class="text-muted mb-0">Modifica los datos y permisos del rol.</p>
    </div>
    <a href="<?= base_url('roles') ?>" class="btn btn-outline-secondary">
        <i class="bx bx-arrow-back"></i> Volver
    </a>
</div>

<form action="<?= base_url('roles/' . $role['id']) ?>" method="POST" autocomplete="off">
    <?= csrf_field() ?>
    <input type="hidden" name="_method" value="PUT">

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name"
                           class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                           value="<?= esc($role['name']) ?>"
                           placeholder="Ej: Administrador" required>
                    <?php if (isset($errors['name'])): ?>
                        <div class="invalid-feedback"><?= $errors['name'] ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label for="scope" class="form-label">Alcance <span class="text-danger">*</span></label>
                    <select name="scope" id="scope" class="form-select <?= isset($errors['scope']) ? 'is-invalid' : '' ?>" required>
                        <?php foreach ($scopes as $key => $label): ?>
                            <option value="<?= $key ?>" <?= $role['scope'] == $key ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['scope'])): ?>
                        <div class="invalid-feedback"><?= $errors['scope'] ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-12">
                    <label for="description" class="form-label">Descripción</label>
                    <textarea name="description" id="description" rows="2"
                              class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>"
                              placeholder="Descripción del rol..."><?= esc($role['description'] ?? '') ?></textarea>
                    <?php if (isset($errors['description'])): ?>
                        <div class="invalid-feedback"><?= $errors['description'] ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label for="status" class="form-label">Estado <span class="text-danger">*</span></label>
                    <select name="status" id="status" class="form-select <?= isset($errors['status']) ? 'is-invalid' : '' ?>" required>
                        <?php foreach ($statuses as $key => $label): ?>
                            <option value="<?= $key ?>" <?= $role['status'] == $key ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['status'])): ?>
                        <div class="invalid-feedback"><?= $errors['status'] ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

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
        <?php
        $agrupados = [];
        foreach ($permissions as $p) {
            $agrupados[$p['module']][] = $p;
        }
        foreach ($agrupados as $modulo => $items):
        ?>
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="perm-group cerrado">
                    <div class="perm-group-header" onclick="toggleGroup(this)">
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
                            <label class="perm-check d-flex align-items-center gap-2">
                                <input class="form-check-input m-0" type="checkbox" name="permissions[]"
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
            <i class="bx bx-save"></i> Guardar cambios
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

function toggleGroup(header) {
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
</script>
<?= $this->endSection() ?>
