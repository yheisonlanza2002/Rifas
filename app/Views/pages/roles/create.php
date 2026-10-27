<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
.perm-group {
    border: 1px solid #e6e8f0;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.2s ease;
}
.perm-group:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}
.perm-group .perm-group-header {
    padding: 10px 14px;
    background: #f8f9fc;
    border-bottom: 1px solid #e6e8f0;
    font-weight: 700;
    font-size: 13.5px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.perm-group .perm-group-header .badge {
    font-size: 11px;
    font-weight: 600;
}
.perm-check {
    cursor: pointer;
    user-select: none;
    transition: background 0.15s ease;
    border-radius: 6px;
    padding: 6px 8px;
    margin-bottom: 2px;
}
.perm-check:hover {
    background: #f8f9fc;
}
.perm-check input[type="checkbox"] {
    cursor: pointer;
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
        <h4 class="mb-1">Crear Rol</h4>
        <p class="text-muted mb-0">Define un nuevo rol y asigna sus permisos.</p>
    </div>
    <a href="<?= base_url('roles') ?>" class="btn btn-outline-secondary">
        <i class="bx bx-arrow-back"></i> Volver
    </a>
</div>

<form action="<?= base_url('roles') ?>" method="POST" autocomplete="off">
    <?= csrf_field() ?>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name"
                           class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                           value="<?= old('name') ?>"
                           placeholder="Ej: Administrador" required>
                    <?php if (isset($errors['name'])): ?>
                        <div class="invalid-feedback"><?= $errors['name'] ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label for="scope" class="form-label">Alcance <span class="text-danger">*</span></label>
                    <select name="scope" id="scope" class="form-select <?= isset($errors['scope']) ? 'is-invalid' : '' ?>" required>
                        <?php foreach ($scopes as $key => $label): ?>
                            <option value="<?= $key ?>" <?= old('scope') == $key ? 'selected' : '' ?>>
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
                              placeholder="Descripción del rol..."><?= old('description') ?></textarea>
                    <?php if (isset($errors['description'])): ?>
                        <div class="invalid-feedback"><?= $errors['description'] ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label for="status" class="form-label">Estado <span class="text-danger">*</span></label>
                    <select name="status" id="status" class="form-select <?= isset($errors['status']) ? 'is-invalid' : '' ?>" required>
                        <?php foreach ($statuses as $key => $label): ?>
                            <option value="<?= $key ?>" <?= old('status', 'ACTIVE') == $key ? 'selected' : '' ?>>
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
                <div class="perm-group h-100">
                    <div class="perm-group-header">
                        <span>
                            <i class="bx bx-folder"></i>
                            <?= esc(ucfirst($modulo)) ?>
                        </span>
                        <div class="d-flex gap-1 align-items-center">
                            <span class="badge bg-light text-dark"><?= count($items) ?></span>
                            <button type="button" class="module-toggle" onclick="toggleModule(this)">
                                Todos
                            </button>
                        </div>
                    </div>
                    <div class="p-2">
                        <?php foreach ($items as $p): ?>
                            <label class="perm-check d-flex align-items-center gap-2">
                                <input class="form-check-input m-0" type="checkbox" name="permissions[]"
                                       value="<?= $p['id'] ?>"
                                       <?= in_array($p['id'], old('permissions', [])) ? 'checked' : '' ?>>
                                <span class="small"><?= esc($p['name']) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="mt-4 d-flex gap-2">
        <button type="submit" class="btn btn-primary px-4">
            <i class="bx bx-save"></i> Guardar rol
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

function toggleModule(btn) {
    const card = btn.closest('.perm-group');
    const checkboxes = card.querySelectorAll('.perm-check input[type=checkbox]');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    checkboxes.forEach(cb => cb.checked = !allChecked);
}
</script>
<?= $this->endSection() ?>
