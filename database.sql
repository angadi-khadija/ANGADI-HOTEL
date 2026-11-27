CREATE DATABASE IF NOT EXISTS choise_hotel
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE choise_hotel;

CREATE TABLE IF NOT EXISTS clients (
    client_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(254) NOT NULL,
    phone VARCHAR(30) NULL,
    password_hash VARCHAR(255) NOT NULL,
    date_of_birth DATE NULL,
    address_line VARCHAR(255) NULL,
    city VARCHAR(100) NULL,
    country VARCHAR(100) NULL,
    postal_code VARCHAR(20) NULL,
    account_status ENUM('active', 'disabled') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (client_id),
    UNIQUE KEY uq_clients_email (email)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS admins (
    admin_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(254) NOT NULL,
    phone VARCHAR(30) NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('owner', 'manager', 'receptionist') NOT NULL DEFAULT 'receptionist',
    account_status ENUM('active', 'disabled') NOT NULL DEFAULT 'active',
    last_login_at TIMESTAMP NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (admin_id),
    UNIQUE KEY uq_admins_email (email)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS rooms (
    room_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    room_number VARCHAR(20) NOT NULL,
    room_type VARCHAR(80) NOT NULL,
    floor_number SMALLINT NULL,
    capacity TINYINT UNSIGNED NOT NULL DEFAULT 1,
    nightly_rate DECIMAL(10, 2) NOT NULL,
    description TEXT NULL,
    room_status ENUM('active', 'maintenance', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (room_id),
    UNIQUE KEY uq_rooms_number (room_number),
    KEY idx_rooms_status_type (room_status, room_type)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS reservations (
    reservation_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    client_id BIGINT UNSIGNED NOT NULL,
    room_id BIGINT UNSIGNED NOT NULL,
    check_in DATE NOT NULL,
    check_out DATE NOT NULL,
    guest_count TINYINT UNSIGNED NOT NULL DEFAULT 1,
    nightly_rate DECIMAL(10, 2) NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    reservation_status ENUM('pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled')
        NOT NULL DEFAULT 'pending',
    special_requests TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (reservation_id),
    KEY idx_reservations_client (client_id),
    KEY idx_reservations_room_dates (room_id, check_in, check_out),
    KEY idx_reservations_status (reservation_status),
    CONSTRAINT fk_reservations_client
        FOREIGN KEY (client_id) REFERENCES clients (client_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_reservations_room
        FOREIGN KEY (room_id) REFERENCES rooms (room_id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;