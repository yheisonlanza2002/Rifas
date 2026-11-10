<?php
// Helper: detectar si la URL actual coincide con un prefijo
$currentUri = current_url(true)->getPath();
function isActive(string $path): string {
    $currentUri = current_url(true)->getPath();
    return str_contains($currentUri, $path) ? 'mm-active' : '';
}
function isActiveLink(string $path): string {
    $currentUri = current_url(true)->getPath();
    return str_contains($currentUri, $path) ? 'active' : '';
}
?>
<div class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        <div>
            <img src="<?= base_url('assets/images/logo-icon.png') ?>" class="logo-icon" alt="logo icon">
        </div>
        <div>
            <h4 class="logo-text">Rifas</h4>
        </div>
        <div class="toggle-icon ms-auto"><i class='bx bx-arrow-back'></i></div>
    </div>

    <!--navigation-->
    <ul class="metismenu" id="menu">

        <!-- INICIO -->
        <li class="menu-label">Inicio</li>

        <li class="<?= isActive('/dashboard') ?>">
            <a href="<?= base_url('dashboard') ?>" class="<?= isActiveLink('/dashboard') ?>">
                <div class="parent-icon"><i class='bx bx-home-circle'></i></div>
                <div class="menu-title">Dashboard</div>
            </a>
        </li>

        <!-- GESTIÓN -->
        <li class="menu-label">Gestión</li>

        <li class="<?= isActive('/rifas') ?>">
            <a href="<?= base_url('rifas') ?>" class="<?= isActiveLink('/rifas') ?>">
                <div class="parent-icon"><i class='bx bx-gift'></i></div>
                <div class="menu-title">Rifas</div>
            </a>
        </li>

        <li class="<?= isActive('/premios') ?>">
            <a href="<?= base_url('premios') ?>" class="<?= isActiveLink('/premios') ?>">
                <div class="parent-icon"><i class='bx bx-trophy'></i></div>
                <div class="menu-title">Premios</div>
            </a>
        </li>

        <li class="<?= isActive('/boletas') ?>">
            <a href="<?= base_url('boletas') ?>" class="<?= isActiveLink('/boletas') ?>">
                <div class="parent-icon"><i class='bx bx-ticket'></i></div>
                <div class="menu-title">Boletas</div>
            </a>
        </li>

        <li class="<?= isActive('/sorteos') ?>">
            <a href="<?= base_url('sorteos') ?>" class="<?= isActiveLink('/sorteos') ?>">
                <div class="parent-icon"><i class='bx bx-dice-5'></i></div>
                <div class="menu-title">Sorteos</div>
            </a>
        </li>

        <!-- VENTAS -->
        <li class="menu-label">Ventas</li>

        <li class="<?= isActive('/ventas') ?>">
            <a href="<?= base_url('ventas') ?>" class="<?= isActiveLink('/ventas') ?>">
                <div class="parent-icon"><i class='bx bx-cart-alt'></i></div>
                <div class="menu-title">Ventas (POS)</div>
            </a>
        </li>

        <li class="<?= isActive('/pagos') ?>">
            <a href="<?= base_url('pagos') ?>" class="<?= isActiveLink('/pagos') ?>">
                <div class="parent-icon"><i class='bx bx-credit-card'></i></div>
                <div class="menu-title">Pagos</div>
            </a>
        </li>

        <li class="<?= isActive('/participantes') ?>">
            <a href="<?= base_url('participantes') ?>" class="<?= isActiveLink('/participantes') ?>">
                <div class="parent-icon"><i class='bx bx-user-pin'></i></div>
                <div class="menu-title">Participantes</div>
            </a>
        </li>

        <!-- REPORTES -->
        <li class="menu-label">Reportes</li>

        <li class="<?= isActive('/reportes') ?>">
            <a href="<?= base_url('reportes') ?>" class="<?= isActiveLink('/reportes') ?>">
                <div class="parent-icon"><i class='bx bx-bar-chart-alt-2'></i></div>
                <div class="menu-title">Reportes</div>
            </a>
        </li>

        <!-- TIENDA WEB -->
        <li class="menu-label">Tienda Web</li>

        <li>
            <a href="<?= base_url('tienda') ?>" target="_blank">
                <div class="parent-icon"><i class='bx bx-store-alt'></i></div>
                <div class="menu-title">Ver Tienda <i class='bx bx-link-external ms-1 font-13'></i></div>
            </a>
        </li>

        <!-- CONFIGURACIÓN -->
        <li class="menu-label">Configuración</li>

        <li class="<?= isActive('/pasarelas') ?>">
            <a href="<?= base_url('pasarelas') ?>" class="<?= isActiveLink('/pasarelas') ?>">
                <div class="parent-icon"><i class='bx bx-wallet-alt'></i></div>
                <div class="menu-title">Pasarelas de Pago</div>
            </a>
        </li>

        <li class="<?= isActive('/plantillas-email') ?>">
            <a href="<?= base_url('plantillas-email') ?>" class="<?= isActiveLink('/plantillas-email') ?>">
                <div class="parent-icon"><i class='bx bx-envelope'></i></div>
                <div class="menu-title">Plantillas Email</div>
            </a>
        </li>

        <!-- ADMINISTRACIÓN -->
        <li class="menu-label">Administración</li>

        <li class="<?= isActive('/usuarios') ?>">
            <a href="<?= base_url('usuarios') ?>" class="<?= isActiveLink('/usuarios') ?>">
                <div class="parent-icon"><i class='bx bx-group'></i></div>
                <div class="menu-title">Usuarios</div>
            </a>
        </li>

        <li class="<?= isActive('/roles') ?>">
            <a href="<?= base_url('roles') ?>" class="<?= isActiveLink('/roles') ?>">
                <div class="parent-icon"><i class='bx bx-shield-quarter'></i></div>
                <div class="menu-title">Roles y Permisos</div>
            </a>
        </li>

    </ul>
    <!--end navigation-->
</div>
<!--end sidebar wrapper -->
