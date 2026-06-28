-- =====================================================================
-- kitinfo.ru — схема базы данных
-- СУБД: MySQL 8.0+ / MariaDB 10.5+
-- Кодировка: utf8mb4, движок: InnoDB (нужен для внешних ключей)
-- =====================================================================

CREATE DATABASE IF NOT EXISTS kitinfo
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE kitinfo;

-- ---------------------------------------------------------------------
-- 1. organizations — клиентские организации
-- ---------------------------------------------------------------------
CREATE TABLE organizations (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(255)      NOT NULL,
    inn           VARCHAR(12)       NULL,
    phone         VARCHAR(20)       NULL,
    email         VARCHAR(150)      NULL,
    address       VARCHAR(255)      NULL,
    created_at    DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_organizations_inn (inn)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 2. users — учётные записи (роли: user, admin; "гость" = не аутентифицирован)
-- ---------------------------------------------------------------------
CREATE TABLE users (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_id INT UNSIGNED      NULL,
    full_name       VARCHAR(150)      NOT NULL,
    email           VARCHAR(150)      NOT NULL,
    phone           VARCHAR(20)       NULL,
    password_hash   VARCHAR(255)      NOT NULL,
    role            ENUM('user','admin') NOT NULL DEFAULT 'user',
    is_active       TINYINT(1)        NOT NULL DEFAULT 1,
    failed_attempts SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    locked_until    DATETIME          NULL,
    created_at      DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_users_email (email),
    KEY idx_users_role (role),
    CONSTRAINT fk_users_organization
        FOREIGN KEY (organization_id) REFERENCES organizations(id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 3. services — каталог услуг
-- ---------------------------------------------------------------------
CREATE TABLE services (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code          VARCHAR(40)       NOT NULL,
    title         VARCHAR(150)      NOT NULL,
    category      ENUM('1c','outsource','server','kkm') NOT NULL,
    description   TEXT              NULL,
    price_from    DECIMAL(10,2)     NULL,
    is_active     TINYINT(1)        NOT NULL DEFAULT 1,
    sort_order    SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    created_at    DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_services_code (code)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 4. requests — заявки от гостей и пользователей
-- ---------------------------------------------------------------------
CREATE TABLE requests (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         INT UNSIGNED      NULL,
    organization_id INT UNSIGNED      NULL,
    service_id      INT UNSIGNED      NULL,
    guest_name      VARCHAR(150)      NULL,
    guest_phone     VARCHAR(20)       NULL,
    guest_email     VARCHAR(150)      NULL,
    message         TEXT              NULL,
    status          ENUM('new','in_progress','done','rejected') NOT NULL DEFAULT 'new',
    admin_comment   TEXT              NULL,
    created_at      DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_requests_status (status),
    KEY idx_requests_created (created_at),
    CONSTRAINT fk_requests_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_requests_organization
        FOREIGN KEY (organization_id) REFERENCES organizations(id)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_requests_service
        FOREIGN KEY (service_id) REFERENCES services(id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 5. logs — журнал действий (для админ-панели / аудита безопасности)
-- ---------------------------------------------------------------------
CREATE TABLE logs (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED      NULL,
    action      VARCHAR(60)       NOT NULL,
    details     VARCHAR(255)      NULL,
    ip_address  VARCHAR(45)       NULL,
    user_agent  VARCHAR(255)      NULL,
    created_at  DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_logs_action (action),
    KEY idx_logs_created (created_at),
    CONSTRAINT fk_logs_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;
