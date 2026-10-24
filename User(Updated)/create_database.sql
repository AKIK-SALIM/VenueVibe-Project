-- ============================================================
-- VenueVibe - Database Creation & Table Schema Definition
-- File: create_database.sql
-- ============================================================

DROP DATABASE IF EXISTS venuevibe;
CREATE DATABASE venuevibe CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE venuevibe;

-- ------------------------------------------------------------
-- 1. USERS TABLE
-- ------------------------------------------------------------
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('student', 'faculty', 'dean', 'admin') NOT NULL DEFAULT 'student',
    is_admin TINYINT(1) NOT NULL DEFAULT 0,
    student_id VARCHAR(50) DEFAULT NULL,
    department VARCHAR(100) DEFAULT NULL,
    phone VARCHAR(50) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 2. VENUES TABLE
-- Shared by the User portal (catalog view) and the Admin
-- portal (facility management view). `status` is the public
-- booking state ('Available'/'Booked'); `admin_status` is the
-- facility management state ('Active'/'Maintenance'/'Draft').
-- ------------------------------------------------------------
CREATE TABLE venues (
    id VARCHAR(50) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type VARCHAR(50) NOT NULL,
    location VARCHAR(150) NOT NULL,
    quad VARCHAR(100) NOT NULL DEFAULT 'North Academic Quad',
    building VARCHAR(100) NOT NULL DEFAULT '',
    floor VARCHAR(50) NOT NULL DEFAULT '',
    suite_code VARCHAR(50) NOT NULL DEFAULT '',
    capacity INT NOT NULL,
    standing INT NOT NULL DEFAULT 0,
    infrastructure VARCHAR(50) NOT NULL,
    setup VARCHAR(100) NOT NULL DEFAULT 'Standard Config',
    slots VARCHAR(50) NOT NULL DEFAULT 'Open Access',
    rating DECIMAL(2,1) NOT NULL DEFAULT 0.0,
    status VARCHAR(20) NOT NULL DEFAULT 'Available',
    admin_status VARCHAR(20) NOT NULL DEFAULT 'Active',
    image VARCHAR(255) NOT NULL,
    thumbnail VARCHAR(255) NOT NULL,
    about TEXT NOT NULL,
    review_count INT DEFAULT 0,
    attendee_email VARCHAR(150) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 3. EQUIPMENT TABLE
-- ------------------------------------------------------------
CREATE TABLE equipment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venue_id VARCHAR(50) NOT NULL,
    item VARCHAR(100) NOT NULL,
    FOREIGN KEY (venue_id) REFERENCES venues(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 4. SCHEDULE TABLE
-- ------------------------------------------------------------
CREATE TABLE schedule (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venue_id VARCHAR(50) NOT NULL,
    event_date VARCHAR(20) NOT NULL,
    title VARCHAR(150) NOT NULL,
    time_range VARCHAR(50) NOT NULL,
    host VARCHAR(150) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'confirmed',
    FOREIGN KEY (venue_id) REFERENCES venues(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 5. REVIEWS TABLE
-- ------------------------------------------------------------
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venue_id VARCHAR(50) NOT NULL,
    initials VARCHAR(5) NOT NULL,
    reviewer_name VARCHAR(150) NOT NULL,
    time_ago VARCHAR(50) NOT NULL,
    review_text TEXT NOT NULL,
    rating TINYINT NOT NULL DEFAULT 5,
    FOREIGN KEY (venue_id) REFERENCES venues(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 6. REQUESTS TABLE
-- ------------------------------------------------------------
CREATE TABLE requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    venue_name VARCHAR(100) NOT NULL,
    venue_location VARCHAR(150) NOT NULL,
    booking_date DATE NOT NULL,
    booking_time VARCHAR(50) NOT NULL,
    purpose VARCHAR(100) NOT NULL,
    description TEXT,
    attachment VARCHAR(255) NULL,
    attendee_email VARCHAR(150) NULL,
    attendee_notified TINYINT(1) NOT NULL DEFAULT 0,
    status VARCHAR(20) NOT NULL DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_booking (venue_name, booking_date, booking_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 7. MESSAGES TABLE
-- ------------------------------------------------------------
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    sender_name VARCHAR(150) NOT NULL,
    text TEXT NOT NULL,
    time_sent VARCHAR(50) NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 8. NOTIFICATIONS TABLE
-- ------------------------------------------------------------
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    title VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    type VARCHAR(50) NOT NULL DEFAULT 'general',
    time_ago VARCHAR(50) NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 9. ANNOUNCEMENTS TABLE
-- Campus-wide announcements published from the Admin portal
-- ------------------------------------------------------------
CREATE TABLE announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    category VARCHAR(50) NOT NULL DEFAULT 'Event Notice',
    priority VARCHAR(50) NOT NULL DEFAULT 'Normal',
    status VARCHAR(20) NOT NULL DEFAULT 'Active',
    status_label VARCHAR(120) NOT NULL DEFAULT 'Active / Live',
    audience VARCHAR(150) NOT NULL DEFAULT 'All Faculty & Students IC',
    time_text VARCHAR(120) NOT NULL DEFAULT '',
    author VARCHAR(150) NOT NULL DEFAULT 'Admin User',
    reads_count VARCHAR(20) NOT NULL DEFAULT '0',
    delivery VARCHAR(20) NOT NULL DEFAULT '100%',
    tags VARCHAR(255) NOT NULL DEFAULT '',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
