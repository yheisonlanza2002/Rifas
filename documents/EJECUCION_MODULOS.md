# Plan de Ejecución por Módulos - Sistema de Rifas

**Fecha de inicio:** 02/10/2026  
**Base técnica completada:** CodeIgniter 4 + Rocker Admin (SweetAlert2 local) + Login funcional + Migraciones + Seeders  
**Metodología:** Trabajar un módulo a la vez, de forma incremental y verificada, antes de pasar al siguiente.

---

## 🎯 REGLAS GLOBALES DEL PROYECTO

### 1. Validaciones centralizadas
Todas las validaciones de formularios viven en `app/Config/Validation.php` como grupos de reglas con nombres:

```php
public array $userCreate = [
    'name' => 'required|min_length[3]|max_length[100]',
    // ...
];
```

Uso en controladores:
```php
if (!$this->validateData($this->request->getPost(), 'userCreate')) {
    return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
}
```

> Compatibilidad: se mantiene `$validationRules` en modelos SOLO para operaciones internas; los formularios usan los grupos de `Config/Validation`.

### 2. Toasts con barra de progreso por color
El helper `toast()` y `showToast()` (JS) aplican estos colores a la barra de progreso del toast:

| Tipo | Color barra | Uso |
|------|-------------|-----|
| `success` | 🟢 Verde (#198754) | Operaciones exitosas |
| `warning` | 🟡 Amarillo (#ffc107) | Advertencias |
| `error` | 🔴 Rojo (#dc3545) | Errores |
| `info` | 🔵 Azul (#0dcaf0) | Informativo |
| `question` | ⚪ Gris (#6c757d) | Preguntas |

Implementación: `app/Helpers/alert_helper.php` → función `toast()` agrega `customClass.timerProgressBar` + hook `didOpen` que pinta la barra. Versión JS en `partials/scripts.php` → `showToast()`.

### 3. Flujo POST estándar por módulo
```
GET  /modulo              → listado (tabla)
GET  /modulo/crear        → formulario crear
POST /modulo              → guardar (valida → flash toast → redirect)
GET  /modulo/{id}/editar  → formulario editar
POST /modulo/{id}         → actualizar (valida → flash toast → redirect)
GET  /modulo/{id}/eliminar→ confirmación SweetAlert2 (data-confirm) → eliminar
```

Errores de validación → `redirect()->back()->withInput()->with('errors', [...])` y la vista muestra errores en cada input + toast rojo.

### 4. Rutas protegidas
Todos los módulos usan `['filter' => 'auth']`. Login redirige al dashboard tras éxito.

---

## 📦 MAPA DE MÓDULOS

| # | Módulo | Tablas BD | Depende de | Estado |
|---|--------|-----------|------------|--------|
| 1 | **Usuarios y Roles** | users, roles, permissions, user_roles | Login (hecho) | ✅ COMPLETADO |
| 2 | **Participantes** | participants, participant_tags | Módulo 1 | ✅ COMPLETADO |
| 3 | **Rifas** | raffles, raffle_settings | Módulo 1 | ✅ COMPLETADO |
| 4 | **Premios** | prizes | Módulo 3 | ✅ COMPLETADO |
| 5 | **Boletas/Tickets** | tickets, ticket_numbers, reservations | Módulos 3 | ✅ COMPLETADO |
| 6 | **Ventas** | sales, sale_items | Módulos 2, 5 | ✅ COMPLETADO |
| 7 | **Pagos y Pasarelas** | payments, payment_gateways, refunds, webhooks | Módulo 6 | ✅ COMPLETADO |
| 8 | **Sorteos y Ganadores** | draws, winners, ticket_events | Módulos 3, 5, 7 | ✅ COMPLETADO |
| 9 | **Notificaciones y Emails** | notifications, email_templates | Módulo 6, 8 | ⬜ |
| 10 | **Reportes y Dashboard** | (consultas a todas) | Módulos 1-8 | ⬜ |
| 11 | **Configuración General** | companies, settings | Módulo 1 | ⬜ |
| 12 | **Auditoría** | audit_logs | Todos | ⬜ |

---

## 🔧 MÓDULO 1: Usuarios y Roles (✅ COMPLETADO 02/10/2026)

**Objetivo:** CRUD completo de usuarios con asignación de roles, y vista de roles (solo lectura inicial).

**Archivos creados/modificados:**

| Tarea | Archivo | Descripción | Estado |
|-------|---------|-------------|--------|
| 1.1 | `app/Config/Validation.php` | Grupos `userLogin`, `userCreate`, `userUpdate` (con regla de placeholder `id`) | ✅ |
| 1.2 | `app/Helpers/alert_helper.php` | `toast()` con `customClass.timerProgressBar` por color | ✅ |
| 1.3 | `app/Views/partials/scripts.php` | `showToast()` JS con barra coloreada | ✅ |
| 1.4 | `public/assets/css/swal2-extra.css` | Clases `.swal2-progress-*` (verde/amarillo/rojo/azul/gris) | ✅ |
| 1.5 | `app/Controllers/Users.php` | Controlador CRUD completo | ✅ |
| 1.6 | `app/Views/pages/users/index.php` | Listado tabla usuarios con roles | ✅ |
| 1.7 | `app/Views/pages/users/create.php` | Formulario crear | ✅ |
| 1.8 | `app/Views/pages/users/edit.php` | Formulario editar | ✅ |
| 1.9 | `app/Views/pages/users/_form.php` | Formulario compartido (create/edit) | ✅ |
| 1.10 | `app/Views/partials/field_error.php` | Partial reutilizable de errores por campo | ✅ |
| 1.11 | `app/Config/Routes.php` | Grupo rutas `/usuarios` | ✅ |
| 1.12 | `app/Views/partials/sidebar.php` | Enlace "Usuarios" en menú | ✅ |
| 1.13 | `app/Models/UserModel.php` | `skipValidation` (evita doble validación con Config) | ✅ |
| 1.14 | `app/Controllers/Auth.php` | Refactorizado a `validateData('userLogin')` | ✅ |

**Bugs corregidos durante el módulo:**
- `UserModel` tenía regla `password` requerida pero el campo real es `password_hash` → insert fallaba silenciosamente (FK user_id=0). Solución: `skipValidation=true` en modelo, validación solo en Config.
- `is_unique[users.email,id,{id}]` requiere regla explícita para el campo `id` → excepción `No validation rules for the placeholder: "id"`. Solución: regla `id => required|is_natural_no_zero` en `userUpdate`.

**Pruebas E2E (todas ✅):**
| Prueba | Resultado |
|--------|-----------|
| Lista usuarios con roles y badges | ✅ |
| Crear con datos inválidos → errores por input + toast rojo | ✅ |
| Crear válido → 303 lista + toast verde con barra success | ✅ |
| Editar: carga datos y roles marcados | ✅ |
| Actualizar (nombre, teléfono, roles múltiples) → toast verde | ✅ |
| is_unique ignora al usuario actual en edición | ✅ |
| Eliminar → soft delete + toast verde | ✅ |
| Auto-eliminación bloqueada → toast amarillo (warning) | ✅ |

---

## 🔧 MÓDULOS SIGUIENTES (resumen)

### Módulo 2: Participantes ✅ COMPLETADO (02/10/2026)
CRUD de participantes (clientes que compran boletas). Tabla `participants`. Tags opcionales (pendiente).

**Archivos creados:**

| Archivo | Descripción |
|---------|-------------|
| `app/Config/Validation.php` | Grupos `participantCreate`, `participantUpdate` (documento único, teléfono obligatorio, fecha nacimiento, género M/F/O) |
| `app/Models/ParticipantModel.php` | Modelo con `forCompany()` (alcance multi-empresa), `findByDocument()` |
| `app/Controllers/Participants.php` | CRUD completo con aislamiento por empresa |
| `app/Views/pages/participants/index.php` | Listado con documento, ciudad, estado |
| `app/Views/pages/participants/create.php` / `edit.php` | Formularios |
| `app/Views/pages/participants/_form.php` | Formulario compartido (11 campos: documento, nombre, contacto, ubicación, género, notas) |

**Pruebas E2E (todas ✅):**
| Prueba | Resultado |
|--------|-----------|
| Lista vacía con mensaje "No hay participantes" | ✅ |
| Crear inválido → errores por input (tipo doc, nombre corto, email inválido) | ✅ |
| Crear válido → 303 lista + toast verde | ✅ |
| Documento duplicado → "Ya existe un participante con este documento" | ✅ |
| Editar: carga datos, select documento marcado, ciudad | ✅ |
| Actualizar (tipo doc CC→NIT, nombre, ciudad) → toast verde | ✅ |
| Eliminar → soft delete + toast verde | ✅ |

**Bug corregido:** `instanceof array` no es válido en PHP (solo clases) → cambiado a `is_array()` en `Participants.php`.

---


### Módulo 3: Rifas ✅ COMPLETADO (03/10/2026)
CRUD de rifas: nombre, código, cantidad boletas, precio, rango de números, fechas, estado (DRAFT→ACTIVE→CLOSED). Configuración por rifa desde `raffle_settings`.

**Archivos creados/modificados:**

| Tarea | Archivo | Descripción | Estado |
|-------|---------|-------------|--------|
| 3.1 | `app/Models/RaffleModel.php` | Modelo con `forCompany()`, `findByCode()`, soft deletes | ✅ |
| 3.2 | `app/Config/Validation.php` | Grupos `raffleCreate`, `raffleUpdate` (18 campos) | ✅ |
| 3.3 | `app/Controllers/Raffles.php` | Controlador CRUD completo | ✅ |
| 3.4 | `app/Views/pages/raffles/index.php` | Listado tabla rifas con estado | ✅ |
| 3.5 | `app/Views/pages/raffles/create.php` | Formulario crear | ✅ |
| 3.6 | `app/Views/pages/raffles/edit.php` | Formulario editar | ✅ |
| 3.7 | `app/Views/pages/raffles/_form.php` | Formulario compartido (18 campos) | ✅ |
| 3.8 | `app/Config/Routes.php` | Grupo rutas `/rifas` | ✅ |
| 3.9 | `app/Views/partials/sidebar.php` | Enlace "Rifas" en menú | ✅ |

**Campos del formulario:**
- Nombre, código, descripción
- Estado (DRAFT/ACTIVE/CLOSED)
- Cantidad de boletas, números por boleta
- Rango de números (inicio, fin, longitud)
- Modo de asignación (SEQUENTIAL/RANDOM/MANUAL)
- Modo de selección (BOTH/SELECTION/ASSIGNMENT)
- Precio, moneda (COP/USD/EUR), comisión
- Minutos de reserva, fechas de inicio/fin
- Términos y condiciones

**Características:**
- Código único por empresa (`is_unique[raffles.code]`)
- Aislamiento por empresa (multi-tenant)
- Soft delete
- Generación de UUIDs
- Validaciones centralizadas en Config/Validation.php

### Módulo 4: Premios ✅ COMPLETADO (03/10/2026)
Premios por rifa: nombre, descripción, posición del sorteo, imagen/valor.

**Archivos creados/modificados:**

| Tarea | Archivo | Descripción | Estado |
|-------|---------|-------------|--------|
| 4.1 | `app/Models/PrizeModel.php` | Modelo con `forCompany()`, `findByPosition()`, soft deletes | ✅ |
| 4.2 | `app/Config/Validation.php` | Grupos `prizeCreate`, `prizeUpdate` (10 campos) | ✅ |
| 4.3 | `app/Controllers/Prizes.php` | Controlador CRUD completo | ✅ |
| 4.4 | `app/Views/pages/prizes/index.php` | Listado tabla premios con rifa y posición | ✅ |
| 4.5 | `app/Views/pages/prizes/create.php` | Formulario crear | ✅ |
| 4.6 | `app/Views/pages/prizes/edit.php` | Formulario editar | ✅ |
| 4.7 | `app/Views/pages/prizes/_form.php` | Formulario compartido (10 campos) | ✅ |
| 4.8 | `app/Config/Routes.php` | Grupo rutas `/premios` | ✅ |
| 4.9 | `app/Views/partials/sidebar.php` | Enlace "Premios" en menú | ✅ |

**Campos del formulario:**
- Rifa (select con rifas activas de la empresa)
- Nombre, descripción
- Posición del sorteo (único por rifa)
- Cantidad, valor, moneda
- Ruta de imagen, probabilidades (odds)
- Estado (ACTIVE/INACTIVE)

**Características:**
- Posición única por rifa (`uq_prizes_raffle_position`)
- Aislamiento por empresa (multi-tenant)
- Soft delete
- Generación de UUIDs
- Validaciones centralizadas en Config/Validation.php

### Módulo 5: Boletas/Tickets ✅ COMPLETADO (03/10/2026)
Generación masiva de boletas según `ticket_quantity` y `number_start/end`. Estados: AVAILABLE, RESERVED, SOLD, CANCELLED. Reservas con expiración (`reservations`).

**Archivos creados/modificados:**

| Tarea | Archivo | Descripción | Estado |
|-------|---------|-------------|--------|
| 5.1 | `app/Models/TicketModel.php` | Modelo con `forCompany()`, `findByCode()`, `countByStatus()` | ✅ |
| 5.2 | `app/Config/Validation.php` | Grupos `ticketCreate`, `ticketUpdate` (8 campos) | ✅ |
| 5.3 | `app/Controllers/Tickets.php` | Controlador CRUD completo | ✅ |
| 5.4 | `app/Views/pages/tickets/index.php` | Listado con filtro por rifa y estado | ✅ |
| 5.5 | `app/Views/pages/tickets/create.php` | Formulario crear | ✅ |
| 5.6 | `app/Views/pages/tickets/edit.php` | Formulario editar | ✅ |
| 5.7 | `app/Views/pages/tickets/_form.php` | Formulario compartido (8 campos) | ✅ |
| 5.8 | `app/Config/Routes.php` | Grupo rutas `/boletas` | ✅ |
| 5.9 | `app/Views/partials/sidebar.php` | Enlace "Boletas" en menú | ✅ |

**Campos del formulario:**
- Rifa (select con rifas de la empresa)
- Código (único por rifa), prefijo serial
- Precio, estado (AVAILABLE/RESERVED/SOLD/CANCELLED)
- Cantidad de números, cantidad del paquete

**Características:**
- Código único por rifa (`uq_tickets_raffle_code`)
- Aislamiento por empresa (multi-tenant)
- No se permite eliminar boletas vendidas
- Fechas automáticas según estado (sold_at, cancelled_at, reserved_at)
- Filtro por rifa en el listado

### Módulo 6: Ventas ✅ COMPLETADO (03/10/2026)
Registro de venta: selección de boletas, participante, total calculado, `sale_items` por boleta.

**Archivos creados/modificados:**

| Tarea | Archivo | Descripción | Estado |
|-------|---------|-------------|--------|
| 6.1 | `app/Models/SaleModel.php` | Modelo con `forCompany()`, `findByCode()`, `countByStatus()` | ✅ |
| 6.2 | `app/Config/Validation.php` | Grupos `saleCreate`, `saleUpdate` (12 campos) | ✅ |
| 6.3 | `app/Controllers/Sales.php` | Controlador CRUD completo | ✅ |
| 6.4 | `app/Views/pages/sales/index.php` | Listado con filtro por estado | ✅ |
| 6.5 | `app/Views/pages/sales/create.php` | Formulario crear | ✅ |
| 6.6 | `app/Views/pages/sales/edit.php` | Formulario editar | ✅ |
| 6.7 | `app/Views/pages/sales/_form.php` | Formulario compartido (12 campos) | ✅ |
| 6.8 | `app/Config/Routes.php` | Grupo rutas `/ventas` | ✅ |
| 6.9 | `app/Views/partials/sidebar.php` | Enlace "Ventas" en menú | ✅ |

**Campos del formulario:**
- Participante (select con participantes de la empresa)
- Código de venta (único por empresa)
- Estado (PENDING/PAID/CANCELLED)
- Cantidad, moneda
- Subtotal, descuento, impuesto, comisión
- Total, monto pagado
- Notas

**Características:**
- Código único por empresa (`uq_sales_company_code`)
- Aislamiento por empresa (multi-tenant)
- No se permite eliminar ventas pagadas
- Fechas automáticas según estado (sold_at, cancelled_at)
- Cálculo automático de monto pendiente
- Filtro por estado en el listado

### Módulo 7: Pagos y Pasarelas ✅ COMPLETADO (03/10/2026)
Registro de pagos por venta: pasarela, referencia, estado. CRUD de pasarelas (configuración JSON). Webhooks (fase avanzada).

**Archivos creados/modificados:**

| Tarea | Archivo | Descripción | Estado |
|-------|---------|-------------|--------|
| 7.1 | `app/Models/PaymentModel.php` | Modelo con `forCompany()`, `findByReference()`, `countByStatus()` | ✅ |
| 7.2 | `app/Models/PaymentGatewayModel.php` | Modelo con `forCompany()`, `findBySlug()`, soft deletes | ✅ |
| 7.3 | `app/Config/Validation.php` | Grupos `paymentCreate`, `paymentUpdate`, `gatewayCreate`, `gatewayUpdate` | ✅ |
| 7.4 | `app/Controllers/Payments.php` | Controlador CRUD completo | ✅ |
| 7.5 | `app/Controllers/PaymentGateways.php` | Controlador CRUD completo | ✅ |
| 7.6 | `app/Views/pages/payment/index.php` | Listado con filtro por estado | ✅ |
| 7.7 | `app/Views/pages/payments/create.php` / `edit.php` | Formularios | ✅ |
| 7.8 | `app/Views/pages/payments/_form.php` | Formulario compartido (10 campos) | ✅ |
| 7.9 | `app/Views/pages/payment_gateways/index.php` | Listado de pasarelas | ✅ |
| 7.10 | `app/Views/pages/payment_gateways/create.php` / `edit.php` | Formularios | ✅ |
| 7.11 | `app/Views/pages/payment_gateways/_form.php` | Formulario compartido (7 campos) | ✅ |
| 7.12 | `app/Config/Routes.php` | Grupos rutas `/pagos` y `/pasarelas` | ✅ |
| 7.13 | `app/Views/partials/sidebar.php` | Enlaces "Pagos" y "Pasarelas" en menú | ✅ |

**Campos del formulario de pagos:**
- Venta (select con ventas de la empresa)
- Pasarela (select con pasarelas activas)
- Monto, comisión, monto neto
- Método de pago, estado (PENDING/COMPLETED/FAILED/REFUNDED)
- Referencia de transacción, pasarela (texto)

**Campos del formulario de pasarelas:**
- Nombre, slug, driver
- Modo de prueba, estado (ACTIVE/INACTIVE)
- Orden

**Características:**
- Slug único por empresa (`uq_payment_gateways_company_slug`)
- Aislamiento por empresa (multi-tenant)
- No se permite eliminar pagos completados
- Fecha de pago automática al completar
- Filtro por estado en el listado de pagos
- Soft delete en pasarelas

### Módulo 8: Sorteos y Ganadores ✅ COMPLETADO (03/10/2026)
Programar sorteos (`draws`), ejecutar sorteo con selección aleatoria verificable (seed + result_hash), registrar ganadores (`winners`), eventos de boleta (`ticket_events`).

**Archivos creados/modificados:**

| Tarea | Archivo | Descripción | Estado |
|-------|---------|-------------|--------|
| 8.1 | `app/Models/DrawModel.php` | Modelo con `forCompany()`, `findByRaffle()`, `findPendingByRaffle()`, `countByStatus()` | ✅ |
| 8.2 | `app/Models/WinnerModel.php` | Modelo con `forCompany()`, `findByDraw()`, `findByParticipant()`, `countByStatus()` | ✅ |
| 8.3 | `app/Models/TicketEventModel.php` | Modelo con `forCompany()`, `findByTicket()`, `findByRaffle()` | ✅ |
| 8.4 | `app/Config/Validation.php` | Grupos `drawCreate`, `drawUpdate` (7 campos) | ✅ |
| 8.5 | `app/Controllers/Draws.php` | Controlador CRUD completo + ejecución de sorteo | ✅ |
| 8.6 | `app/Views/pages/draws/index.php` | Listado tabla sorteos con estado y acciones | ✅ |
| 8.7 | `app/Views/pages/draws/create.php` | Formulario crear | ✅ |
| 8.8 | `app/Views/pages/draws/edit.php` | Formulario editar | ✅ |
| 8.9 | `app/Views/pages/draws/_form.php` | Formulario compartido (7 campos) | ✅ |
| 8.10 | `app/Views/pages/draws/results.php` | Resultados del sorteo con ganadores | ✅ |
| 8.11 | `app/Config/Routes.php` | Grupo rutas `/sorteos` | ✅ |
| 8.12 | `app/Views/partials/sidebar.php` | Enlace "Sorteos" en menú | ✅ |

**Campos del formulario:**
- Rifa (select con rifas activas de la empresa)
- Nombre del sorteo
- Fecha programada (datetime)
- Método (MANUAL/RANDOM/EXTERNAL_API/BLOCKCHAIN)
- Semilla (opcional, para reproducibilidad)
- Referencia externa (opcional)

**Características:**
- Selección aleatoria verificable con seed + result_hash (SHA-256)
- Estados: SCHEDULED → EXECUTING → COMPLETED / CANCELLED
- No se puede editar/ejecutar sorteos completados
- No se puede eliminar sorteos completados
- Registro automático de eventos de boleta (ticket_events)
- Aislamiento por empresa (multi-tenant)
- Generación de UUIDs
- Validaciones centralizadas en Config/Validation.php

### Módulo 9: Notificaciones y Emails ⬜
Editor de plantillas (`email_templates`) con variables `{name}`, `{sale_code}`. Cola de notificaciones (`notifications`).

### modulo 10 roles y permisos
crear modulos con los roles y asignacion de permisos segun sea el rol ademas que se pueda editar que permisos tenga ese usuario 


### Módulo 11: Reportes y Dashboard ⬜
Dashboard con datos reales: ventas del mes, boletas vendidas/disponibles, rifas activas, ingresos. Gráficos con Chart.js (ya incluido).

### Módulo 12: Configuración General ⬜
Datos de empresa, logo, zona horaria, moneda. Editor de `companies.settings` JSON .



### Módulo 13: Auditoría ⬜
Registro automático de acciones (crear/editar/eliminar) en `audit_logs` desde eventos de modelos.

### modulo de tienda web 

en eeste modulo haremos la tienda web para la venta de estas voletas en linea con pagos por nequi o cualquier banco atraves de un codigo qr por el momentop antes de que se ponga a funcionar con wompi 

tenemos que hacer algo muy bien hecho akgo moderno y quye valla acorde con el estilo de nuestro sitio web 

### modulo 14 backups y soporte o apoyo 
en este modulo haremos un modulo de backups para tener control de los eliminados y apoyo al cliente que compre en la web 

### y mapas para saber donde hay puntos autorizados para vender las rifas

en este modulo haremos un mapa en tiempo real donde pondremos los puntos de venta autorizados para hacer estas ventas 
### modulo 16 documentacion  
en este modulo vamos a documentat todo el sistema que hace cada modulo como una gia de enseñansa 

## 🔧 MÓDULO: ROLES Y PERMISOS ✅ COMPLETADO (05/10/2026)

**Objetivo:** CRUD completo de roles y permisos con asignación de permisos a roles.

**Archivos creados/modificados:**

| Tarea | Archivo | Descripción | Estado |
|-------|---------|-------------|--------|
| R.1 | `app/Models/RoleModel.php` | Modelo con `getPermissions()`, `syncPermissions()`, `countUsers()` | ✅ |
| R.2 | `app/Models/PermissionModel.php` | Modelo con `getModules()`, `findByModuleAndAction()`, `countRoles()` | ✅ |
| R.3 | `app/Config/Validation.php` | Grupos `roleCreate`, `roleUpdate`, `permissionCreate`, `permissionUpdate` | ✅ |
| R.4 | `app/Controllers/Roles.php` | Controlador CRUD completo + asignación de permisos | ✅ |
| R.5 | `app/Controllers/Permissions.php` | Controlador CRUD completo | ✅ |
| R.6 | `app/Views/pages/roles/index.php` | Listado tabla roles con contadores | ✅ |
| R.7 | `app/Views/pages/roles/create.php` | Formulario crear | ✅ |
| R.8 | `app/Views/pages/roles/edit.php` | Formulario editar | ✅ |
| R.9 | `app/Views/pages/roles/_form.php` | Formulario compartido con checkboxes de permisos | ✅ |
| R.10 | `app/Views/pages/permissions/index.php` | Listado tabla permisos con contadores | ✅ |
| R.11 | `app/Views/pages/permissions/create.php` | Formulario crear | ✅ |
| R.12 | `app/Views/pages/permissions/edit.php` | Formulario editar | ✅ |
| R.13 | `app/Views/pages/permissions/_form.php` | Formulario compartido | ✅ |
| R.14 | `app/Config/Routes.php` | Grupos rutas `/roles` y `/permisos` | ✅ |
| R.15 | `app/Views/partials/sidebar.php` | Enlaces "Roles" y "Permisos" en menú | ✅ |

**Campos del formulario de roles:**
- Nombre, descripción
- Alcance (COMPANY/SYSTEM)
- Permisos (checkboxes agrupados por módulo)
- Estado (ACTIVE/INACTIVE)

**Campos del formulario de permisos:**
- Nombre, módulo, acción
- Descripción, guard_name (web/api)
- Estado (ACTIVE/INACTIVE)

**Características:**
- Roles del sistema protegidos (no se pueden editar/eliminar)
- No se pueden eliminar roles con usuarios asignados
- No se pueden eliminar permisos asignados a roles
- Sincronización de permisos en roles (tabla role_permissions)
- Contadores de usuarios y permisos en listados
- Soft delete en roles y permisos
- Validaciones centralizadas en Config/Validation.php

**Rediseño de vistas (05/10/2026):**
- Vista de roles: Tabla simple con columnas Rol, Descripción, Permisos, Sistema, Acciones
- Vista de permisos del rol: Grid de tarjetas modernas con permisos agrupados por módulo
- Funciones JavaScript: Marcar/Desmarcar todo, Toggle por módulo
- Estilos CSS personalizados con bordes redondeados y transiciones suaves
- Módulo unificado: Roles y Permisos en un solo módulo (eliminado Permissions.php separado)

---

## 📝 BITÁCORA DE MÓDULOS

| Fecha | Módulo | Acción | Estado | Observaciones |
|-------|--------|--------|--------|---------------|
| 02/10/2026 | Base | Plan creado | ✅ | Documento inicial |
| 02/10/2026 | Global | Validaciones centralizadas + toast coloreado | ✅ | `Validation.php` + `alert_helper` + `scripts.php` + `swal2-extra.css` |
| 02/10/2026 | 1 | Usuarios y Roles — CRUD completo verificado | ✅ | Bugs: doble validación modelo + placeholder `id` |
| 02/10/2026 | 2 | Participantes — CRUD completo verificado | ✅ | Doc único por empresa, bugs: `instanceof array` → `is_array()` |
| 03/10/2026 | 3 | Rifas — CRUD completo verificado | ✅ | 18 campos, código único por empresa, modos de asignación/selección |
| 03/10/2026 | 4 | Premios — CRUD completo verificado | ✅ | 10 campos, posición única por rifa, FK a raffles |
| 03/10/2026 | 5 | Boletas/Tickets — CRUD completo verificado | ✅ | 8 campos, código único por rifa, estados con fechas automáticas |
| 03/10/2026 | 6 | Ventas — CRUD completo verificado | ✅ | 12 campos, código único por empresa, totales con impuestos y comisiones |
| 03/10/2026 | 7 | Pagos y Pasarelas — CRUD completo verificado | ✅ | Pagos: 10 campos, Pasarelas: 7 campos, slug único por empresa |
| 03/10/2026 | 8 | Sorteos y Ganadores — CRUD completo + ejecución verificada | ✅ | 7 campos, seed + result_hash SHA-256, eventos de boleta automáticos |
| 05/10/2026 | R | Roles y Permisos — CRUD completo verificado | ✅ | Roles: 4 campos, Permisos: 6 campos, asignación de permisos a roles |

---

*Ir actualizando al completar cada módulo/tarea.*
