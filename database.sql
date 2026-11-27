CREATE DATABASE IF NOT EXISTS choise_hotel
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE choise_hotel;

CREATE TABLE IF NOT EXISTS clients (
    client_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(254) NOT NULL,
    phone VARCHAR(30) NULL,
    password_hash VARCHAR(255) NOT NULL,
    date_of_birth DATE NULL,
    address_line VARCHAR(255) NULL,
    city VARCHAR(100) NULL,
    country VARCHAR(100) NULL,
    PRIMARY KEY (client_id),
    UNIQUE KEY uq_clients_email (email)
);

CREATE TABLE IF NOT EXISTS admins (
    admin_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(254) NOT NULL,
    phone VARCHAR(30) NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('owner', 'manager', 'receptionist') NOT NULL DEFAULT 'receptionist',
    PRIMARY KEY (admin_id),
    UNIQUE KEY uq_admins_email (email)
);

CREATE TABLE IF NOT EXISTS rooms (
    room_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    room_number VARCHAR(20) NOT NULL,
    room_type VARCHAR(80) NOT NULL,
    floor_number SMALLINT NULL,
    capacity TINYINT UNSIGNED NOT NULL DEFAULT 1,
    description TEXT NULL,
    room_status ENUM('active', 'maintenance', 'inactive') NOT NULL DEFAULT 'active',
    PRIMARY KEY (room_id),
    UNIQUE KEY uq_rooms_number (room_number),
    KEY idx_rooms_status_type (room_status, room_type)
);

CREATE TABLE IF NOT EXISTS reservations (
    reservation_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    client_id INT UNSIGNED NOT NULL,
    room_id INT UNSIGNED NOT NULL,
    check_in DATE NOT NULL,
    check_out DATE NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    reservation_status ENUM('pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled')
        NOT NULL DEFAULT 'pending', 
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
);