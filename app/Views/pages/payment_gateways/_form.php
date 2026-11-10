<?php
$errors  ??= null;
$gateway ??= [];
$isEdit  ??= false;

// Decodificar configuración existente
$config = [];
if (!empty($gateway['config'])) {
    $decoded = json_decode($gateway['config'], true);
    $config  = is_array($decoded) ? $decoded : [];
}
?>

<form method="post" action="<?= $action ?>" class="row g-3" id="gateway-form">
    <?= csrf_field() ?>

    <!-- ── Datos básicos ─────────────────────────── -->
    <div class="col-12">
        <h6 class="fw-semibold text-muted mb-3"><i class='bx bx-cog me-1'></i>Datos Generales</h6>
    </div>

    <div class="col-md-6">
        <label for="name" class="form-label">Nombre <span class="text-danger">*</span></label>
        <input type="text" class="form-control <?= !empty($errors['name'] ?? null) ? 'is-invalid' : '' ?>"
               id="name" name="name" placeholder="Ej: Wompi Colombia"
               value="<?= esc(old('name') ?? ($gateway['name'] ?? '')) ?>">
        <?= view('partials/field_error', ['errors' => $errors, 'field' => 'name']) ?>
    </div>

    <div class="col-md-6">
        <label for="slug" class="form-label">Slug / Identificador <span class="text-danger">*</span></label>
        <input type="text" class="form-control <?= !empty($errors['slug'] ?? null) ? 'is-invalid' : '' ?>"
               id="slug" name="slug" placeholder="Ej: wompi-co"
               value="<?= esc(old('slug') ?? ($gateway['slug'] ?? '')) ?>">
        <?= view('partials/field_error', ['errors' => $errors, 'field' => 'slug']) ?>
        <div class="form-text">Identificador único, sin espacios ni caracteres especiales.</div>
    </div>

    <div class="col-md-4">
        <label for="driver" class="form-label">Tipo de pasarela <span class="text-danger">*</span></label>
        <select class="form-select <?= !empty($errors['driver'] ?? null) ? 'is-invalid' : '' ?>"
                id="driver" name="driver" onchange="mostrarConfigDriver()">
            <option value="">— Seleccionar —</option>
            <option value="wompi"         <?= (old('driver') ?? ($gateway['driver'] ?? '')) === 'wompi'          ? 'selected' : '' ?>>Wompi (Colombia)</option>
            <option value="mercado_pago"  <?= (old('driver') ?? ($gateway['driver'] ?? '')) === 'mercado_pago'   ? 'selected' : '' ?>>Mercado Pago</option>
            <option value="payu"          <?= (old('driver') ?? ($gateway['driver'] ?? '')) === 'payu'           ? 'selected' : '' ?>>PayU</option>
            <option value="manual"        <?= (old('driver') ?? ($gateway['driver'] ?? '')) === 'manual'         ? 'selected' : '' ?>>Manual (Efectivo / Transferencia)</option>
        </select>
        <?= view('partials/field_error', ['errors' => $errors, 'field' => 'driver']) ?>
    </div>

    <div class="col-md-4">
        <label for="test_mode" class="form-label">Modo <span class="text-danger">*</span></label>
        <?php $testMode = old('test_mode') ?? ($gateway['test_mode'] ?? 1); ?>
        <select class="form-select" id="test_mode" name="test_mode">
            <option value="1" <?= $testMode == 1 ? 'selected' : '' ?>>🧪 Sandbox (Pruebas)</option>
            <option value="0" <?= $testMode == 0 ? 'selected' : '' ?>>🚀 Producción</option>
        </select>
    </div>

    <div class="col-md-2">
        <label for="sort_order" class="form-label">Orden</label>
        <input type="number" class="form-control" id="sort_order" name="sort_order" min="0"
               value="<?= esc(old('sort_order') ?? ($gateway['sort_order'] ?? 0)) ?>">
    </div>

    <div class="col-md-2">
        <label for="status" class="form-label">Estado <span class="text-danger">*</span></label>
        <?php $status = old('status') ?? ($gateway['status'] ?? 'ACTIVE'); ?>
        <select class="form-select" id="status" name="status">
            <?php foreach ($statuses as $key => $label): ?>
                <option value="<?= $key ?>" <?= $status === $key ? 'selected' : '' ?>><?= esc($label) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- ── Configuración Wompi ───────────────────── -->
    <div id="config-wompi" class="col-12 driver-config" style="display:none;">
        <div class="card border-warning border-2">
            <div class="card-header bg-warning bg-opacity-10">
                <h6 class="mb-0 fw-semibold text-warning">
                    <i class='bx bx-key me-2'></i>Credenciales Wompi
                    <span class="badge bg-warning text-dark ms-2 small">Requerido</span>
                </h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info d-flex gap-2 py-2 small mb-3">
                    <i class='bx bx-info-circle mt-1'></i>
                    <div>Obtén tus credenciales en <a href="https://commerce.wompi.co" target="_blank">commerce.wompi.co</a> → Desarrolladores → API Keys.
                    Para sandbox usa las llaves que dicen <code>pub_test_xxx</code> / <code>prv_test_xxx</code>.</div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Llave Pública <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class='bx bx-key'></i></span>
                            <input type="text" class="form-control" name="config[pub_key]"
                                   placeholder="pub_test_xxxxxxxxxxxx"
                                   value="<?= esc(old('config.pub_key') ?? ($config['pub_key'] ?? '')) ?>">
                        </div>
                        <div class="form-text">Empieza con <code>pub_test_</code> (sandbox) o <code>pub_prod_</code></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Llave Privada <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class='bx bx-lock-alt'></i></span>
                            <input type="password" class="form-control" name="config[prv_key]"
                                   placeholder="prv_test_xxxxxxxxxxxx"
                                   value="<?= esc(old('config.prv_key') ?? ($config['prv_key'] ?? '')) ?>">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword(this)">
                                <i class='bx bx-show'></i>
                            </button>
                        </div>
                        <div class="form-text">Empieza con <code>prv_test_</code> (sandbox) o <code>prv_prod_</code></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Llave de Eventos (Webhook) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class='bx bx-webhook'></i></span>
                            <input type="password" class="form-control" name="config[events_key]"
                                   placeholder="prod_integrity_xxxxxxxxxxxx"
                                   value="<?= esc(old('config.events_key') ?? ($config['events_key'] ?? '')) ?>">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword(this)">
                                <i class='bx bx-show'></i>
                            </button>
                        </div>
                        <div class="form-text">Llave para verificar firmas de los eventos del webhook.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">URL del Webhook (configurar en Wompi)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class='bx bx-link'></i></span>
                            <input type="text" class="form-control bg-light" readonly
                                   value="<?= base_url('webhook/wompi') ?>">
                            <button class="btn btn-outline-secondary" type="button"
                                    onclick="navigator.clipboard.writeText('<?= base_url('webhook/wompi') ?>'); showToast('URL copiada', 'success', 2000)">
                                <i class='bx bx-copy'></i>
                            </button>
                        </div>
                        <div class="form-text">Copia esta URL y agrégala en tu panel de Wompi → Configuración → Webhooks.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Configuración Mercado Pago ────────────── -->
    <div id="config-mercado_pago" class="col-12 driver-config" style="display:none;">
        <div class="card border-info border-2">
            <div class="card-header bg-info bg-opacity-10">
                <h6 class="mb-0 fw-semibold text-info">
                    <i class='bx bx-key me-2'></i>Credenciales Mercado Pago
                </h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info d-flex gap-2 py-2 small mb-3">
                    <i class='bx bx-info-circle mt-1'></i>
                    <div>Obtén tus credenciales en <a href="https://www.mercadopago.com.co/developers" target="_blank">mercadopago.com.co/developers</a> → Tus aplicaciones → Credenciales.</div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Public Key</label>
                        <input type="text" class="form-control" name="config[mp_public_key]"
                               placeholder="APP_USR-xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"
                               value="<?= esc(old('config.mp_public_key') ?? ($config['mp_public_key'] ?? '')) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Access Token</label>
                        <input type="password" class="form-control" name="config[mp_access_token]"
                               placeholder="TEST-xxxxxxxxxxxxxxxxxxx..."
                               value="<?= esc(old('config.mp_access_token') ?? ($config['mp_access_token'] ?? '')) ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Configuración PayU ────────────────────── -->
    <div id="config-payu" class="col-12 driver-config" style="display:none;">
        <div class="card border-secondary border-2">
            <div class="card-header bg-secondary bg-opacity-10">
                <h6 class="mb-0 fw-semibold">
                    <i class='bx bx-key me-2'></i>Credenciales PayU
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Merchant ID</label>
                        <input type="text" class="form-control" name="config[payu_merchant_id]"
                               placeholder="508029"
                               value="<?= esc(old('config.payu_merchant_id') ?? ($config['payu_merchant_id'] ?? '')) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Account ID</label>
                        <input type="text" class="form-control" name="config[payu_account_id]"
                               placeholder="512321"
                               value="<?= esc(old('config.payu_account_id') ?? ($config['payu_account_id'] ?? '')) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">API Key</label>
                        <input type="password" class="form-control" name="config[payu_api_key]"
                               placeholder="4Vj8eK4rloUd..."
                               value="<?= esc(old('config.payu_api_key') ?? ($config['payu_api_key'] ?? '')) ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Configuración Manual ──────────────────── -->
    <div id="config-manual" class="col-12 driver-config" style="display:none;">
        <div class="card border-success border-2">
            <div class="card-header bg-success bg-opacity-10">
                <h6 class="mb-0 fw-semibold text-success">
                    <i class='bx bx-money me-2'></i>Pago Manual — Instrucciones
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Banco / Entidad</label>
                        <input type="text" class="form-control" name="config[banco]"
                               placeholder="Ej: Bancolombia"
                               value="<?= esc(old('config.banco') ?? ($config['banco'] ?? '')) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tipo de Cuenta</label>
                        <select class="form-select" name="config[tipo_cuenta]">
                            <?php $tipoCuenta = old('config.tipo_cuenta') ?? ($config['tipo_cuenta'] ?? 'ahorros'); ?>
                            <option value="ahorros"  <?= $tipoCuenta === 'ahorros'  ? 'selected' : '' ?>>Ahorros</option>
                            <option value="corriente"<?= $tipoCuenta === 'corriente'? 'selected' : '' ?>>Corriente</option>
                            <option value="nequi"    <?= $tipoCuenta === 'nequi'    ? 'selected' : '' ?>>Nequi</option>
                            <option value="daviplata"<?= $tipoCuenta === 'daviplata'? 'selected' : '' ?>>Daviplata</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Número de Cuenta</label>
                        <input type="text" class="form-control" name="config[numero_cuenta]"
                               placeholder="Ej: 123-456789-01"
                               value="<?= esc(old('config.numero_cuenta') ?? ($config['numero_cuenta'] ?? '')) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Titular de la Cuenta</label>
                        <input type="text" class="form-control" name="config[titular]"
                               placeholder="Ej: Juan Pérez"
                               value="<?= esc(old('config.titular') ?? ($config['titular'] ?? '')) ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Instrucciones para el comprador</label>
                        <textarea class="form-control" name="config[instrucciones]" rows="3"
                                  placeholder="Ej: Realiza el pago y envía el comprobante por WhatsApp al número..."><?= esc(old('config.instrucciones') ?? ($config['instrucciones'] ?? '')) ?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Botones ───────────────────────────────── -->
    <div class="col-12">
        <hr>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary px-4">
                <i class="bx bx-save me-1"></i> <?= $isEdit ? 'Actualizar Pasarela' : 'Guardar Pasarela' ?>
            </button>
            <a href="<?= base_url('pasarelas') ?>" class="btn btn-outline-secondary px-4">
                <i class="bx bx-arrow-back me-1"></i> Volver
            </a>
        </div>
    </div>
</form>

<script>
function mostrarConfigDriver() {
    const driver = document.getElementById('driver').value;
    document.querySelectorAll('.driver-config').forEach(el => el.style.display = 'none');
    if (driver) {
        const target = document.getElementById('config-' + driver);
        if (target) target.style.display = '';
    }
    // Auto-completar slug desde el nombre si está vacío
    const nameEl = document.getElementById('name');
    const slugEl = document.getElementById('slug');
    if (slugEl.value === '' && nameEl.value !== '') {
        slugEl.value = nameEl.value.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '');
    }
}

function togglePassword(btn) {
    const input = btn.previousElementSibling;
    if (input.type === 'password') {
        input.type = 'text';
        btn.querySelector('i').className = 'bx bx-hide';
    } else {
        input.type = 'password';
        btn.querySelector('i').className = 'bx bx-show';
    }
}

// Auto-slug desde nombre
document.getElementById('name').addEventListener('blur', function() {
    const slug = document.getElementById('slug');
    if (slug.value === '') {
        slug.value = this.value.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '');
    }
});

// Inicializar al cargar (modo edición)
document.addEventListener('DOMContentLoaded', mostrarConfigDriver);
</script>
