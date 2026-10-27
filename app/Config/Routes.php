<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Redirige la raíz al dashboard (el filtro auth manda a login si no hay sesión)
$routes->get('/', 'Dashboard::index', ['filter' => 'auth']);
$routes->get('dashboard', 'Dashboard::index', ['filter' => 'auth']);

// Autenticación (rutas públicas)
$routes->group('auth', static function ($routes) {
    $routes->get('login', 'Auth::login');
    $routes->post('login', 'Auth::attemptLogin');
    $routes->get('logout', 'Auth::logout');
    $routes->get('register', 'Auth::register');
    $routes->get('forgot-password', 'Auth::forgotPassword');
    $routes->get('reset-password', 'Auth::resetPassword');
});

// Componentes (demos - protegidos)
$routes->group('componentes', ['filter' => 'auth'], static function ($routes) {
    $routes->get('alertas', 'Components::alerts');
    $routes->post('alertas', 'Components::alertsPost');
    $routes->get('alertas/demo/(:segment)', 'Components::alertsDemo/$1');
});

// MÓDULO 1: Usuarios
$routes->group('usuarios', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Users::index');
    $routes->get('crear', 'Users::create');
    $routes->post('/', 'Users::store');
    $routes->get('(:num)/editar', 'Users::edit/$1');
    $routes->post('(:num)', 'Users::update/$1');
    $routes->get('(:num)/eliminar', 'Users::delete/$1');
});

// MÓDULO 2: Participantes
$routes->group('participantes', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Participants::index');
    $routes->get('crear', 'Participants::create');
    $routes->post('/', 'Participants::store');
    $routes->get('(:num)/editar', 'Participants::edit/$1');
    $routes->post('(:num)', 'Participants::update/$1');
    $routes->get('(:num)/eliminar', 'Participants::delete/$1');
});

// MÓDULO 3: Rifas
$routes->group('rifas', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Raffles::index');
    $routes->get('crear', 'Raffles::create');
    $routes->post('/', 'Raffles::store');
    $routes->get('(:num)/editar', 'Raffles::edit/$1');
    $routes->post('(:num)', 'Raffles::update/$1');
    $routes->get('(:num)/eliminar', 'Raffles::delete/$1');
});

// MÓDULO 4: Premios
$routes->group('premios', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Prizes::index');
    $routes->get('crear', 'Prizes::create');
    $routes->post('/', 'Prizes::store');
    $routes->get('(:num)/editar', 'Prizes::edit/$1');
    $routes->post('(:num)', 'Prizes::update/$1');
    $routes->get('(:num)/eliminar', 'Prizes::delete/$1');
});

// MÓDULO 5: Boletas/Tickets
$routes->group('boletas', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Tickets::index');
    $routes->get('crear', 'Tickets::create');
    $routes->post('/', 'Tickets::store');
    $routes->get('(:num)/editar', 'Tickets::edit/$1');
    $routes->post('(:num)', 'Tickets::update/$1');
    $routes->get('(:num)/eliminar', 'Tickets::delete/$1');
});

// MÓDULO 6: Ventas
$routes->group('ventas', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Sales::index');
    $routes->get('crear', 'Sales::create');
    $routes->post('/', 'Sales::store');
    $routes->get('(:num)/editar', 'Sales::edit/$1');
    $routes->post('(:num)', 'Sales::update/$1');
    $routes->get('(:num)/eliminar', 'Sales::delete/$1');
});

// MÓDULO 7: Pagos y Pasarelas
$routes->group('pagos', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Payments::index');
    $routes->get('crear', 'Payments::create');
    $routes->post('/', 'Payments::store');
    $routes->get('(:num)/editar', 'Payments::edit/$1');
    $routes->post('(:num)', 'Payments::update/$1');
    $routes->get('(:num)/eliminar', 'Payments::delete/$1');
});

$routes->group('pasarelas', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'PaymentGateways::index');
    $routes->get('crear', 'PaymentGateways::create');
    $routes->post('/', 'PaymentGateways::store');
    $routes->get('(:num)/editar', 'PaymentGateways::edit/$1');
    $routes->post('(:num)', 'PaymentGateways::update/$1');
    $routes->get('(:num)/eliminar', 'PaymentGateways::delete/$1');
});

// MÓDULO 8: Sorteos y Ganadores
$routes->group('sorteos', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Draws::index');
    $routes->get('crear', 'Draws::create');
    $routes->post('/', 'Draws::store');
    $routes->get('(:num)/editar', 'Draws::edit/$1');
    $routes->post('(:num)', 'Draws::update/$1');
    $routes->get('(:num)/eliminar', 'Draws::delete/$1');
    $routes->get('(:num)/ejecutar', 'Draws::execute/$1');
    $routes->get('(:num)/resultados', 'Draws::results/$1');
    $routes->get('(:num)/cancelar', 'Draws::cancel/$1');
});

// MÓDULO 9: Notificaciones y Emails
$routes->group('plantillas-email', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'EmailTemplates::index');
    $routes->get('crear', 'EmailTemplates::create');
    $routes->post('/', 'EmailTemplates::store');
    $routes->get('(:num)/editar', 'EmailTemplates::edit/$1');
    $routes->post('(:num)', 'EmailTemplates::update/$1');
    $routes->get('(:num)/eliminar', 'EmailTemplates::delete/$1');
    $routes->get('(:num)/vista-previa', 'EmailTemplates::preview/$1');
});

$routes->group('notificaciones', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Notifications::index');
    $routes->get('(:num)/marcar', 'Notifications::markAsRead/$1');
    $routes->get('(:num)/eliminar', 'Notifications::delete/$1');
    $routes->get('marcar-todas', 'Notifications::markAllAsRead');
});

// MÓDULO: Loterías
$routes->post('loterias/guardar', 'Lotteries::guardar');

// MÓDULO: Roles y Permisos
$routes->group('roles', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Roles::index');
    $routes->get('crear', 'Roles::create');
    $routes->post('/', 'Roles::store');
    $routes->get('(:num)/editar', 'Roles::edit/$1');
    $routes->post('(:num)', 'Roles::update/$1');
    $routes->get('(:num)/eliminar', 'Roles::delete/$1');
    $routes->get('permisos/(:num)', 'Roles::permisos/$1');
    $routes->post('actualizar-permisos/(:num)', 'Roles::actualizarPermisos/$1');
});
