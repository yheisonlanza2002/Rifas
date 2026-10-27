-- ============================================================
-- JF MEDIA | RIFAS | DATABASE V3.0
-- Modelo técnico mejorado (sincronizado con migraciones CI4)
-- MySQL 8.x / InnoDB / utf8mb4
--
-- Regla central:
-- EL CLIENTE BUSCA UN NÚMERO, PERO EL SISTEMA RESERVA,
-- VENDE Y CONTROLA LA BOLETA COMPLETA.
--
-- Mejoras V3.0:
-- - Números de boleta alfanuméricos (VARCHAR)
-- - Soporte para bundles (quantity en sale_items)
-- - Múltiples ganadores por premio (prizes.quantity)
-- - Pasarelas de pago configurables (payment_gateways)
-- - Webhooks para conciliación de pagos
-- - Reembolsos (refunds)
-- - Auditoría general (audit_logs)
-- - Notificaciones y plantillas de email
-- - Tags de participantes (segmentación)
-- - Tokens de API y 2FA en usuarios
-- - Soft deletes en roles/permissions
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS raffle_settings;
DROP TABLE IF EXISTS email_templates;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS winners;
DROP TABLE IF EXISTS draws;
DROP TABLE IF EXISTS ticket_events;
DROP TABLE IF EXISTS refunds;
DROP TABLE IF EXISTS reservations;
DROP TABLE IF EXISTS webhooks;
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS payment_gateways;
DROP TABLE IF EXISTS sale_items;
DROP TABLE IF EXISTS sales;
DROP TABLE IF EXISTS ticket_numbers;
DROP TABLE IF EXISTS tickets;
DROP TABLE IF EXISTS prizes;
DROP TABLE IF EXISTS raffles;
DROP TABLE IF EXISTS participant_taggings;
DROP TABLE IF EXISTS participant_tags;
DROP TABLE IF EXISTS customer_accounts;
DROP TABLE IF EXISTS participants;
DROP TABLE IF EXISTS personal_access_tokens;
DROP TABLE IF EXISTS password_resets;
DROP TABLE IF EXISTS user_roles;
DROP TABLE IF EXISTS role_permissions;
DROP TABLE IF EXISTS permissions;
DROP TABLE IF EXISTS roles;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS companies;

-- ============================================================
-- LOTE 1: CORE & AUTH
-- ============================================================

CREATE TABLE companies (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(180) NOT NULL UNIQUE,
    legal_name VARCHAR(200) NULL,
    document_type VARCHAR(30) NULL,
    document_number VARCHAR(50) NULL,
    email VARCHAR(150) NULL,
    phone VARCHAR(30) NULL,
    address VARCHAR(255) NULL,
    city VARCHAR(100) NULL,
    department VARCHAR(100) NULL,
    country VARCHAR(100) NOT NULL DEFAULT 'Colombia',
    settings JSON NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'ACTIVE',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL,
    deleted_at DATETIME NULL,
    INDEX idx_companies_status (status),
    INDEX idx_companies_deleted (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE,
    company_id BIGINT UNSIGNED NULL,
    name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(30) NULL,
    password_hash VARCHAR(255) NOT NULL,
    avatar VARCHAR(255) NULL,
    timezone VARCHAR(50) NOT NULL DEFAULT 'America/Bogota',
    locale VARCHAR(10) NOT NULL DEFAULT 'es_CO',
    two_factor_enabled TINYINT(1) NOT NULL DEFAULT 0,
    two_factor_secret VARCHAR(100) NULL,
    two_factor_recovery_codes TEXT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'ACTIVE',
    last_login_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL,
    deleted_at DATETIME NULL,
    INDEX idx_users_company (company_id),
    INDEX idx_users_status (status),
    INDEX idx_users_deleted (deleted_at),
    CONSTRAINT fk_users_company
        FOREIGN KEY (company_id) REFERENCES companies(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(80) NOT NULL UNIQUE,
    description VARCHAR(255) NULL,
    scope VARCHAR(20) NOT NULL DEFAULT 'COMPANY',
    is_system TINYINT(1) NOT NULL DEFAULT 0,
    status VARCHAR(20) NOT NULL DEFAULT 'ACTIVE',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL,
    deleted_at DATETIME NULL,
    INDEX idx_roles_scope (scope),
    INDEX idx_roles_status (status),
    INDEX idx_roles_deleted (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE permissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL UNIQUE,
    module VARCHAR(80) NOT NULL,
    action VARCHAR(80) NOT NULL,
    description VARCHAR(255) NULL,
    guard_name VARCHAR(20) NOT NULL DEFAULT 'web',
    status VARCHAR(20) NOT NULL DEFAULT 'ACTIVE',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL,
    deleted_at DATETIME NULL,
    UNIQUE KEY uq_permissions_module_action (module, action),
    INDEX idx_permissions_module (module),
    INDEX idx_permissions_guard (guard_name),
    INDEX idx_permissions_status (status),
    INDEX idx_permissions_deleted (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE role_permissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_id BIGINT UNSIGNED NOT NULL,
    permission_id BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL,
    UNIQUE KEY uq_role_permission (role_id, permission_id),
    CONSTRAINT fk_role_permissions_role
        FOREIGN KEY (role_id) REFERENCES roles(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_role_permissions_permission
        FOREIGN KEY (permission_id) REFERENCES permissions(id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE user_roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    role_id BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL,
    UNIQUE KEY uq_user_role (user_id, role_id),
    CONSTRAINT fk_user_roles_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_user_roles_role
        FOREIGN KEY (role_id) REFERENCES roles(id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE password_resets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL,
    token VARCHAR(255) NOT NULL UNIQUE,
    created_at DATETIME NOT NULL,
    INDEX idx_password_resets_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE personal_access_tokens (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tokenable_type VARCHAR(100) NOT NULL,
    tokenable_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    token CHAR(64) NOT NULL UNIQUE,
    abilities TEXT NULL,
    last_used_at DATETIME NULL,
    expires_at DATETIME NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    INDEX idx_tokens_tokenable (tokenable_type, tokenable_id),
    INDEX idx_tokens_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- LOTE 2: PARTICIPANTES & CUENTAS
-- ============================================================

CREATE TABLE participants (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE,
    company_id BIGINT UNSIGNED NOT NULL,
    document_type VARCHAR(30) NULL,
    document_number VARCHAR(50) NULL,
    name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NULL,
    email VARCHAR(150) NULL,
    phone VARCHAR(30) NULL,
    address VARCHAR(255) NULL,
    city VARCHAR(100) NULL,
    department VARCHAR(100) NULL,
    birth_date DATE NULL,
    gender VARCHAR(20) NULL,
    notes TEXT NULL,
    source VARCHAR(20) NOT NULL DEFAULT 'WEB',
    status VARCHAR(20) NOT NULL DEFAULT 'ACTIVE',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL,
    deleted_at DATETIME NULL,
    INDEX idx_participants_company (company_id),
    INDEX idx_participants_document (company_id, document_type, document_number),
    INDEX idx_participants_email (company_id, email),
    INDEX idx_participants_phone (company_id, phone),
    INDEX idx_participants_status (status),
    INDEX idx_participants_deleted (deleted_at),
    CONSTRAINT fk_participants_company
        FOREIGN KEY (company_id) REFERENCES companies(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE customer_accounts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE,
    participant_id BIGINT UNSIGNED NOT NULL,
    email VARCHAR(150) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email_verification_token VARCHAR(100) NULL,
    email_verification_expires DATETIME NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'ACTIVE',
    email_verified_at DATETIME NULL,
    last_login_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL,
    UNIQUE KEY uq_customer_account_participant (participant_id),
    UNIQUE KEY uq_customer_account_email (email),
    CONSTRAINT fk_customer_accounts_participant
        FOREIGN KEY (participant_id) REFERENCES participants(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE participant_tags (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE,
    company_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(80) NOT NULL,
    color VARCHAR(7) NOT NULL DEFAULT '#3B82F6',
    description VARCHAR(255) NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL,
    UNIQUE KEY uq_participant_tags_company_name (company_id, name),
    INDEX idx_participant_tags_company (company_id),
    CONSTRAINT fk_participant_tags_company
        FOREIGN KEY (company_id) REFERENCES companies(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE participant_taggings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    participant_id BIGINT UNSIGNED NOT NULL,
    tag_id BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL,
    UNIQUE KEY uq_participant_tagging (participant_id, tag_id),
    CONSTRAINT fk_participant_taggings_participant
        FOREIGN KEY (participant_id) REFERENCES participants(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_participant_taggings_tag
        FOREIGN KEY (tag_id) REFERENCES participant_tags(id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- LOTE 3: RIFAS, PREMIOS, BOLETAS
-- ============================================================

CREATE TABLE raffles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE,
    company_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,
    description TEXT NULL,
    code VARCHAR(80) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'DRAFT',

    -- Configuración de generación
    ticket_quantity INT UNSIGNED NOT NULL,
    numbers_per_ticket INT UNSIGNED NOT NULL DEFAULT 1,
    number_start VARCHAR(30) NOT NULL,
    number_end VARCHAR(30) NOT NULL,
    number_length INT UNSIGNED NULL,
    number_assignment_mode VARCHAR(20) NOT NULL DEFAULT 'SEQUENTIAL',
    number_selection_mode VARCHAR(20) NOT NULL DEFAULT 'BOTH',

    ticket_price DECIMAL(15,2) NOT NULL,
    currency CHAR(3) NOT NULL DEFAULT 'COP',
    commission_rate DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    reservation_minutes INT UNSIGNED NOT NULL DEFAULT 15,

    -- Contenido y presentación
    terms_conditions TEXT NULL,
    banner_image VARCHAR(255) NULL,
    gallery JSON NULL,
    settings JSON NULL,

    start_at DATETIME NULL,
    end_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL,
    deleted_at DATETIME NULL,

    UNIQUE KEY uq_raffles_company_code (company_id, code),
    INDEX idx_raffles_company_status (company_id, status),
    INDEX idx_raffles_deleted (deleted_at),

    CONSTRAINT fk_raffles_company
        FOREIGN KEY (company_id) REFERENCES companies(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE prizes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE,
    raffle_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,
    description TEXT NULL,
    position INT UNSIGNED NOT NULL,
    quantity INT UNSIGNED NOT NULL DEFAULT 1,
    value DECIMAL(15,2) NULL,
    currency CHAR(3) NOT NULL DEFAULT 'COP',
    image VARCHAR(255) NULL,
    odds DECIMAL(8,4) NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'ACTIVE',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL,
    deleted_at DATETIME NULL,

    UNIQUE KEY uq_prizes_raffle_position (raffle_id, position),
    INDEX idx_prizes_raffle (raffle_id),
    INDEX idx_prizes_status (status),
    INDEX idx_prizes_deleted (deleted_at),

    CONSTRAINT fk_prizes_raffle
        FOREIGN KEY (raffle_id) REFERENCES raffles(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE tickets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE,
    company_id BIGINT UNSIGNED NOT NULL,
    raffle_id BIGINT UNSIGNED NOT NULL,
    code VARCHAR(80) NOT NULL,
    serial_prefix VARCHAR(20) NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'AVAILABLE',
    price DECIMAL(15,2) NOT NULL,
    number_quantity INT UNSIGNED NOT NULL,
    bundle_quantity INT UNSIGNED NOT NULL DEFAULT 1,

    reserved_at DATETIME NULL,
    reservation_expires_at DATETIME NULL,
    sold_at DATETIME NULL,
    cancelled_at DATETIME NULL,

    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL,

    UNIQUE KEY uq_tickets_raffle_code (raffle_id, code),
    INDEX idx_tickets_company_status (company_id, status),
    INDEX idx_tickets_raffle_status (raffle_id, status),

    CONSTRAINT fk_tickets_company
        FOREIGN KEY (company_id) REFERENCES companies(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_tickets_raffle
        FOREIGN KEY (raffle_id) REFERENCES raffles(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE ticket_numbers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE,
    raffle_id BIGINT UNSIGNED NOT NULL,
    ticket_id BIGINT UNSIGNED NOT NULL,
    number VARCHAR(30) NOT NULL COMMENT 'Número alfanumérico (ej: A-001, 0042)',
    display_number VARCHAR(30) NOT NULL COMMENT 'Número formateado para mostrar',
    is_winner TINYINT(1) NOT NULL DEFAULT 0,
    won_at DATETIME NULL,
    created_at DATETIME NOT NULL,

    UNIQUE KEY uq_ticket_numbers_raffle_number (raffle_id, number),
    UNIQUE KEY uq_ticket_numbers_ticket_number (ticket_id, number),
    INDEX idx_ticket_numbers_ticket (ticket_id),
    INDEX idx_ticket_numbers_display (raffle_id, display_number),
    INDEX idx_ticket_numbers_is_winner (is_winner),

    CONSTRAINT fk_ticket_numbers_raffle
        FOREIGN KEY (raffle_id) REFERENCES raffles(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_ticket_numbers_ticket
        FOREIGN KEY (ticket_id) REFERENCES tickets(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- LOTE 4: VENTAS, PAGOS, RESERVAS
-- ============================================================

CREATE TABLE sales (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE,
    company_id BIGINT UNSIGNED NOT NULL,
    participant_id BIGINT UNSIGNED NOT NULL,
    sale_code VARCHAR(80) NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'PENDING',

    quantity INT UNSIGNED NOT NULL DEFAULT 0,
    subtotal DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    discount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    tax_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    commission_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    total DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    paid_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    pending_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    currency CHAR(3) NOT NULL DEFAULT 'COP',

    notes TEXT NULL,
    sold_at DATETIME NULL,
    expires_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL,
    cancelled_at DATETIME NULL,

    UNIQUE KEY uq_sales_company_code (company_id, sale_code),
    INDEX idx_sales_participant (participant_id),
    INDEX idx_sales_company_status (company_id, status),
    INDEX idx_sales_created (company_id, created_at),

    CONSTRAINT fk_sales_company
        FOREIGN KEY (company_id) REFERENCES companies(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_sales_participant
        FOREIGN KEY (participant_id) REFERENCES participants(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE sale_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE,
    sale_id BIGINT UNSIGNED NOT NULL,
    ticket_id BIGINT UNSIGNED NOT NULL,
    quantity INT UNSIGNED NOT NULL DEFAULT 1,
    unit_price DECIMAL(15,2) NOT NULL,
    discount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    tax_rate DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    tax_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    subtotal DECIMAL(15,2) NOT NULL,
    created_at DATETIME NOT NULL,

    UNIQUE KEY uq_sale_ticket (sale_id, ticket_id),
    UNIQUE KEY uq_ticket_sale (ticket_id),
    INDEX idx_sale_items_sale (sale_id),

    CONSTRAINT fk_sale_items_sale
        FOREIGN KEY (sale_id) REFERENCES sales(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_sale_items_ticket
        FOREIGN KEY (ticket_id) REFERENCES tickets(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE payment_gateways (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE,
    company_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(80) NOT NULL,
    slug VARCHAR(80) NOT NULL,
    driver VARCHAR(50) NOT NULL COMMENT 'wompi, mercado_pago, payu, manual',
    config JSON NOT NULL COMMENT 'Claves y configuración del gateway',
    supported_methods JSON NULL,
    test_mode TINYINT(1) NOT NULL DEFAULT 1,
    status VARCHAR(20) NOT NULL DEFAULT 'ACTIVE',
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL,
    deleted_at DATETIME NULL,

    UNIQUE KEY uq_payment_gateways_company_slug (company_id, slug),
    INDEX idx_payment_gateways_company (company_id),
    INDEX idx_payment_gateways_driver (driver),
    INDEX idx_payment_gateways_status (status),
    INDEX idx_payment_gateways_deleted (deleted_at),

    CONSTRAINT fk_payment_gateways_company
        FOREIGN KEY (company_id) REFERENCES companies(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE,
    company_id BIGINT UNSIGNED NOT NULL,
    sale_id BIGINT UNSIGNED NOT NULL,
    gateway_id BIGINT UNSIGNED NULL,

    amount DECIMAL(15,2) NOT NULL,
    fee_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    net_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    payment_method VARCHAR(40) NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'PENDING',

    transaction_reference VARCHAR(150) NULL,
    gateway VARCHAR(80) NULL,
    raw_request JSON NULL,
    raw_response JSON NULL,
    paid_at DATETIME NULL,

    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL,

    INDEX idx_payments_sale (sale_id),
    INDEX idx_payments_gateway_id (gateway_id),
    INDEX idx_payments_company_status (company_id, status),
    INDEX idx_payments_paid (company_id, paid_at),
    INDEX idx_payments_gateway_reference (gateway, transaction_reference),

    CONSTRAINT fk_payments_company
        FOREIGN KEY (company_id) REFERENCES companies(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_payments_sale
        FOREIGN KEY (sale_id) REFERENCES sales(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_payments_gateway
        FOREIGN KEY (gateway_id) REFERENCES payment_gateways(id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE webhooks (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE,
    gateway_id BIGINT UNSIGNED NULL,
    gateway VARCHAR(80) NOT NULL,
    event_type VARCHAR(100) NOT NULL,
    payload JSON NOT NULL,
    headers JSON NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'RECEIVED',
    processed_at DATETIME NULL,
    error_message TEXT NULL,
    retry_count INT UNSIGNED NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL,

    INDEX idx_webhooks_gateway_id (gateway_id),
    INDEX idx_webhooks_gateway (gateway),
    INDEX idx_webhooks_event_type (event_type),
    INDEX idx_webhooks_status (status),
    INDEX idx_webhooks_processed (processed_at),

    CONSTRAINT fk_webhooks_gateway
        FOREIGN KEY (gateway_id) REFERENCES payment_gateways(id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE reservations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE,
    company_id BIGINT UNSIGNED NOT NULL,
    raffle_id BIGINT UNSIGNED NOT NULL,
    participant_id BIGINT UNSIGNED NOT NULL,
    ticket_id BIGINT UNSIGNED NOT NULL,
    requested_number_id BIGINT UNSIGNED NULL,

    status VARCHAR(30) NOT NULL DEFAULT 'ACTIVE',
    reserved_at DATETIME NOT NULL,
    expires_at DATETIME NOT NULL,
    released_at DATETIME NULL,
    release_reason VARCHAR(255) NULL,

    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL,

    INDEX idx_reservations_ticket (ticket_id),
    INDEX idx_reservations_participant (participant_id),
    INDEX idx_reservations_status_expiry (status, expires_at),

    CONSTRAINT fk_reservations_company
        FOREIGN KEY (company_id) REFERENCES companies(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_reservations_raffle
        FOREIGN KEY (raffle_id) REFERENCES raffles(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_reservations_participant
        FOREIGN KEY (participant_id) REFERENCES participants(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_reservations_ticket
        FOREIGN KEY (ticket_id) REFERENCES tickets(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_reservations_requested_number
        FOREIGN KEY (requested_number_id) REFERENCES ticket_numbers(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE refunds (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE,
    company_id BIGINT UNSIGNED NOT NULL,
    payment_id BIGINT UNSIGNED NOT NULL,
    sale_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    reason VARCHAR(255) NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'PENDING',
    gateway_refund_id VARCHAR(150) NULL,
    raw_response JSON NULL,
    processed_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL,

    INDEX idx_refunds_company (company_id),
    INDEX idx_refunds_payment (payment_id),
    INDEX idx_refunds_sale (sale_id),
    INDEX idx_refunds_status (status),

    CONSTRAINT fk_refunds_company
        FOREIGN KEY (company_id) REFERENCES companies(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_refunds_payment
        FOREIGN KEY (payment_id) REFERENCES payments(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_refunds_sale
        FOREIGN KEY (sale_id) REFERENCES sales(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- LOTE 5: SORTEOS, GANADORES, EVENTOS, AUDITORÍA
-- ============================================================

CREATE TABLE draws (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE,
    company_id BIGINT UNSIGNED NOT NULL,
    raffle_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,
    scheduled_at DATETIME NOT NULL,
    executed_at DATETIME NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'SCHEDULED',
    method ENUM('MANUAL','RANDOM','EXTERNAL_API','BLOCKCHAIN') NULL,
    seed VARCHAR(100) NULL COMMENT 'Semilla para auditoría de aleatoriedad',
    result_hash VARCHAR(64) NULL COMMENT 'Hash del resultado para verificación',
    external_reference VARCHAR(150) NULL,
    created_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL,

    INDEX idx_draws_raffle (raffle_id),
    INDEX idx_draws_status (status),
    INDEX idx_draws_scheduled (company_id, scheduled_at),

    CONSTRAINT fk_draws_company
        FOREIGN KEY (company_id) REFERENCES companies(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_draws_raffle
        FOREIGN KEY (raffle_id) REFERENCES raffles(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_draws_created_by
        FOREIGN KEY (created_by) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE winners (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE,
    draw_id BIGINT UNSIGNED NOT NULL,
    raffle_id BIGINT UNSIGNED NOT NULL,
    prize_id BIGINT UNSIGNED NOT NULL,
    ticket_id BIGINT UNSIGNED NOT NULL,
    ticket_number_id BIGINT UNSIGNED NOT NULL,
    participant_id BIGINT UNSIGNED NOT NULL,

    status VARCHAR(30) NOT NULL DEFAULT 'PENDING',
    announced_at DATETIME NULL,
    notification_sent_at DATETIME NULL,
    claim_deadline DATETIME NULL,
    claimed_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL,

    -- NOTA: Solo unique en (draw_id, ticket_number_id)
    -- Se eliminó unique en (draw_id, prize_id) para soportar
    -- múltiples ganadores por premio (ver prizes.quantity)
    UNIQUE KEY uq_winner_draw_number (draw_id, ticket_number_id),
    INDEX idx_winners_prize (prize_id),
    INDEX idx_winners_participant (participant_id),
    INDEX idx_winners_raffle (raffle_id),
    INDEX idx_winners_status (status),

    CONSTRAINT fk_winners_draw
        FOREIGN KEY (draw_id) REFERENCES draws(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_winners_raffle
        FOREIGN KEY (raffle_id) REFERENCES raffles(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_winners_prize
        FOREIGN KEY (prize_id) REFERENCES prizes(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_winners_ticket
        FOREIGN KEY (ticket_id) REFERENCES tickets(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_winners_ticket_number
        FOREIGN KEY (ticket_number_id) REFERENCES ticket_numbers(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_winners_participant
        FOREIGN KEY (participant_id) REFERENCES participants(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE ticket_events (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE,
    raffle_id BIGINT UNSIGNED NOT NULL,
    ticket_id BIGINT UNSIGNED NOT NULL,
    ticket_number_id BIGINT UNSIGNED NULL,

    event_type VARCHAR(80) NOT NULL,
    previous_status VARCHAR(30) NULL,
    new_status VARCHAR(30) NULL,

    reference_type VARCHAR(80) NULL,
    reference_id BIGINT UNSIGNED NULL,
    description TEXT NULL,
    metadata JSON NULL,

    created_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL,

    INDEX idx_ticket_events_ticket (ticket_id, created_at),
    INDEX idx_ticket_events_number (ticket_number_id, created_at),
    INDEX idx_ticket_events_raffle (raffle_id, created_at),

    CONSTRAINT fk_ticket_events_raffle
        FOREIGN KEY (raffle_id) REFERENCES raffles(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_ticket_events_ticket
        FOREIGN KEY (ticket_id) REFERENCES tickets(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_ticket_events_number
        FOREIGN KEY (ticket_number_id) REFERENCES ticket_numbers(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_ticket_events_created_by
        FOREIGN KEY (created_by) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE,
    auditable_type VARCHAR(100) NOT NULL,
    auditable_id BIGINT UNSIGNED NOT NULL,
    action VARCHAR(50) NOT NULL COMMENT 'created, updated, deleted, restored',
    old_values JSON NULL,
    new_values JSON NULL,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(255) NULL,
    created_at DATETIME NOT NULL,

    INDEX idx_audit_logs_auditable (auditable_type, auditable_id),
    INDEX idx_audit_logs_user (user_id),
    INDEX idx_audit_logs_action (action),
    INDEX idx_audit_logs_created (created_at),

    CONSTRAINT fk_audit_logs_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE notifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE,
    notifiable_type VARCHAR(100) NOT NULL,
    notifiable_id BIGINT UNSIGNED NOT NULL,
    type VARCHAR(100) NOT NULL,
    data JSON NOT NULL,
    read_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL,

    INDEX idx_notifications_notifiable (notifiable_type, notifiable_id),
    INDEX idx_notifications_type (type),
    INDEX idx_notifications_read (read_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE email_templates (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE,
    company_id BIGINT UNSIGNED NULL COMMENT 'NULL = plantilla global',
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    body_html TEXT NOT NULL,
    body_text TEXT NULL,
    variables JSON NULL COMMENT 'Variables disponibles: ["name","code",...]',
    is_global TINYINT(1) NOT NULL DEFAULT 0,
    status VARCHAR(20) NOT NULL DEFAULT 'ACTIVE',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL,
    deleted_at DATETIME NULL,

    UNIQUE KEY uq_email_templates_company_slug (company_id, slug),
    INDEX idx_email_templates_company (company_id),
    INDEX idx_email_templates_slug (slug),
    INDEX idx_email_templates_global (is_global),
    INDEX idx_email_templates_status (status),
    INDEX idx_email_templates_deleted (deleted_at),

    CONSTRAINT fk_email_templates_company
        FOREIGN KEY (company_id) REFERENCES companies(id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE raffle_settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE,
    raffle_id BIGINT UNSIGNED NOT NULL,
    `key` VARCHAR(100) NOT NULL,
    value TEXT NULL,
    type VARCHAR(20) NOT NULL DEFAULT 'string' COMMENT 'string, int, boolean, json, url',
    description VARCHAR(255) NULL,
    is_public TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL,

    UNIQUE KEY uq_raffle_settings_raffle_key (raffle_id, `key`),
    INDEX idx_raffle_settings_raffle (raffle_id),
    INDEX idx_raffle_settings_key (`key`),

    CONSTRAINT fk_raffle_settings_raffle
        FOREIGN KEY (raffle_id) REFERENCES raffles(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- ESTADOS PROPUESTOS
-- ============================================================
-- companies:          ACTIVE / INACTIVE / SUSPENDED
-- users:              ACTIVE / INACTIVE / BLOCKED
-- roles:              ACTIVE / INACTIVE
-- permissions:        ACTIVE / INACTIVE
-- participants:       ACTIVE / INACTIVE / BLOCKED
-- customer_accounts:  ACTIVE / INACTIVE / BLOCKED / PENDING_VERIFICATION
-- raffles:            DRAFT / SCHEDULED / ACTIVE / PAUSED / CLOSED / CANCELLED
-- prizes:             ACTIVE / INACTIVE
-- tickets:            AVAILABLE / RESERVED / SOLD / CANCELLED
-- sales:              PENDING / PARTIALLY_PAID / PAID / EXPIRED / CANCELLED / REFUNDED
-- payments:           PENDING / PROCESSING / APPROVED / REJECTED / REFUNDED / CANCELLED
-- payment_gateways:   ACTIVE / INACTIVE
-- reservations:       ACTIVE / CONVERTED / EXPIRED / CANCELLED
-- refunds:            PENDING / PROCESSING / COMPLETED / REJECTED
-- webhooks:           RECEIVED / PROCESSING / PROCESSED / FAILED
-- draws:              SCHEDULED / IN_PROGRESS / COMPLETED / CANCELLED
-- winners:            PENDING / CONTACTED / CLAIMED / EXPIRED / CANCELLED
-- email_templates:    ACTIVE / INACTIVE
--
-- draws.method:       MANUAL / RANDOM / EXTERNAL_API / BLOCKCHAIN
-- participants.source: WEB / POS / IMPORT / API
--
-- NOTA:
-- Los estados deben validarse en la capa de aplicación (CI4)
-- y en reglas transaccionales del servicio. No se depende del
-- frontend para controlar estados.
-- ============================================================
