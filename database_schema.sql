-- =====================================================
-- CANLAON EVACUEE SYSTEM - DATABASE SCHEMA
-- =====================================================
-- This schema creates all necessary tables for the
-- Canlaon Evacuee System including the new evacuation
-- centers management functionality.
-- =====================================================

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS `resident_management` 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `resident_management`;

-- =====================================================
-- USERS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) UNIQUE,
    `role` ENUM('main_admin', 'admin', 'staff') NOT NULL DEFAULT 'staff',
    `barangay_id` INT(11) NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `last_login` DATETIME NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_username` (`username`),
    INDEX `idx_role` (`role`),
    INDEX `idx_barangay_id` (`barangay_id`),
    INDEX `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- BARANGAYS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS `barangays` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `code` VARCHAR(10) NOT NULL UNIQUE,
    `description` TEXT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_code` (`code`),
    INDEX `idx_name` (`name`),
    INDEX `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- EVACUATION CENTERS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS `evacuation_centers` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(150) NOT NULL,
    `barangay_id` INT(11) NOT NULL,
    `address` TEXT NOT NULL,
    `capacity` INT(11) NOT NULL DEFAULT 0,
    `current_occupancy` INT(11) NOT NULL DEFAULT 0,
    `contact_person` VARCHAR(100) NULL,
    `contact_number` VARCHAR(20) NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_by` INT(11) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_barangay_id` (`barangay_id`),
    INDEX `idx_name` (`name`),
    INDEX `idx_is_active` (`is_active`),
    INDEX `idx_capacity` (`capacity`),
    INDEX `idx_current_occupancy` (`current_occupancy`),
    INDEX `idx_created_by` (`created_by`),
    FOREIGN KEY (`barangay_id`) REFERENCES `barangays`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `chk_capacity_positive` CHECK (`capacity` >= 0),
    CONSTRAINT `chk_occupancy_valid` CHECK (`current_occupancy` >= 0 AND `current_occupancy` <= `capacity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- HOUSEHOLDS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS `households` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `household_head` VARCHAR(100) NOT NULL,
    `address` TEXT NOT NULL,
    `barangay_id` INT(11) NOT NULL,
    `collection_point` VARCHAR(200) NULL,
    `evacuation_vehicle` VARCHAR(100) NULL,
    `vehicle_driver` VARCHAR(100) NULL,
    `assigned_evacuation_center` VARCHAR(150) NULL,
    `phone_number` VARCHAR(20) NULL,
    `control_number` VARCHAR(20) UNIQUE NULL,
    `created_by` INT(11) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_barangay_id` (`barangay_id`),
    INDEX `idx_household_head` (`household_head`),
    INDEX `idx_control_number` (`control_number`),
    INDEX `idx_assigned_evacuation_center` (`assigned_evacuation_center`),
    INDEX `idx_created_by` (`created_by`),
    FOREIGN KEY (`barangay_id`) REFERENCES `barangays`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- RESIDENTS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS `residents` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `household_id` INT(11) NOT NULL,
    `first_name` VARCHAR(50) NOT NULL,
    `last_name` VARCHAR(50) NOT NULL,
    `middle_name` VARCHAR(50) NULL,
    `date_of_birth` DATE NOT NULL,
    `gender` ENUM('male', 'female') NOT NULL,
    `civil_status` ENUM('single', 'married', 'widowed', 'divorced', 'separated') NOT NULL,
    `nationality` VARCHAR(50) NOT NULL DEFAULT 'Filipino',
    `religion` VARCHAR(50) NULL,
    `occupation` VARCHAR(100) NULL,
    `educational_attainment` ENUM('none', 'elementary', 'high_school', 'college', 'post_graduate') NULL,
    `contact_number` VARCHAR(20) NULL,
    `email` VARCHAR(100) NULL,
    `emergency_contact_name` VARCHAR(100) NULL,
    `emergency_contact_number` VARCHAR(20) NULL,
    `emergency_contact_relationship` VARCHAR(50) NULL,
    `has_special_needs` TINYINT(1) NOT NULL DEFAULT 0,
    `special_needs_description` TEXT NULL,
    `is_household_head` TINYINT(1) NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_by` INT(11) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_household_id` (`household_id`),
    INDEX `idx_first_name` (`first_name`),
    INDEX `idx_last_name` (`last_name`),
    INDEX `idx_date_of_birth` (`date_of_birth`),
    INDEX `idx_gender` (`gender`),
    INDEX `idx_has_special_needs` (`has_special_needs`),
    INDEX `idx_is_household_head` (`is_household_head`),
    INDEX `idx_is_active` (`is_active`),
    INDEX `idx_created_by` (`created_by`),
    FOREIGN KEY (`household_id`) REFERENCES `households`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- ID CARDS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS `id_cards` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `resident_id` INT(11) NOT NULL,
    `card_number` VARCHAR(50) NOT NULL UNIQUE,
    `barcode_data` TEXT NOT NULL,
    `status` ENUM('active', 'expired', 'cancelled') NOT NULL DEFAULT 'active',
    `issue_date` DATE NOT NULL,
    `expiry_date` DATE NOT NULL,
    `generated_by` INT(11) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_resident_id` (`resident_id`),
    INDEX `idx_card_number` (`card_number`),
    INDEX `idx_status` (`status`),
    INDEX `idx_issue_date` (`issue_date`),
    INDEX `idx_expiry_date` (`expiry_date`),
    INDEX `idx_generated_by` (`generated_by`),
    FOREIGN KEY (`resident_id`) REFERENCES `residents`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (`generated_by`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- ACTIVITY LOGS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS `activity_logs` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) NULL,
    `action` VARCHAR(100) NOT NULL,
    `table_name` VARCHAR(50) NULL,
    `record_id` INT(11) NULL,
    `description` TEXT NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_action` (`action`),
    INDEX `idx_table_name` (`table_name`),
    INDEX `idx_created_at` (`created_at`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- INSERT DEFAULT DATA
-- =====================================================

-- Insert default barangays
INSERT INTO `barangays` (`name`, `code`, `description`) VALUES
('Barangay 1', 'BRGY01', 'Barangay 1 - Canlaon City'),
('Barangay 2', 'BRGY02', 'Barangay 2 - Canlaon City'),
('Barangay 3', 'BRGY03', 'Barangay 3 - Canlaon City'),
('Barangay 4', 'BRGY04', 'Barangay 4 - Canlaon City'),
('Barangay 5', 'BRGY05', 'Barangay 5 - Canlaon City'),
('Barangay 6', 'BRGY06', 'Barangay 6 - Canlaon City'),
('Barangay 7', 'BRGY07', 'Barangay 7 - Canlaon City'),
('Barangay 8', 'BRGY08', 'Barangay 8 - Canlaon City'),
('Barangay 9', 'BRGY09', 'Barangay 9 - Canlaon City'),
('Barangay 10', 'BRGY10', 'Barangay 10 - Canlaon City');

-- Insert default evacuation centers
INSERT INTO `evacuation_centers` (`name`, `barangay_id`, `address`, `capacity`, `contact_person`, `contact_number`) VALUES
('Canlaon City Elementary School', 1, 'Poblacion, Canlaon City, Negros Oriental', 500, 'School Principal', '09123456789'),
('Canlaon City High School', 2, 'Poblacion, Canlaon City, Negros Oriental', 800, 'School Principal', '09123456790'),
('Barangay 3 Hall', 3, 'Barangay 3, Canlaon City, Negros Oriental', 200, 'Barangay Captain', '09123456791'),
('Canlaon City Gymnasium', 4, 'City Center, Canlaon City, Negros Oriental', 1000, 'City Administrator', '09123456792'),
('Barangay 5 Community Center', 5, 'Barangay 5, Canlaon City, Negros Oriental', 300, 'Barangay Secretary', '09123456793'),
('Canlaon City Church', 6, 'Poblacion, Canlaon City, Negros Oriental', 400, 'Church Administrator', '09123456794'),
('Barangay 7 Multi-Purpose Hall', 7, 'Barangay 7, Canlaon City, Negros Oriental', 250, 'Barangay Captain', '09123456795'),
('Canlaon City College', 8, 'City Center, Canlaon City, Negros Oriental', 600, 'College Administrator', '09123456796'),
('Barangay 9 Health Center', 9, 'Barangay 9, Canlaon City, Negros Oriental', 150, 'Health Worker', '09123456797'),
('Barangay 10 Sports Complex', 10, 'Barangay 10, Canlaon City, Negros Oriental', 350, 'Sports Coordinator', '09123456798');

-- Insert default main admin user
INSERT INTO `users` (`username`, `password`, `full_name`, `email`, `role`) VALUES
('mainadmin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Main Administrator', 'admin@canlaon.gov.ph', 'main_admin');

-- =====================================================
-- CREATE VIEWS FOR REPORTING
-- =====================================================

-- View for evacuation center statistics
CREATE OR REPLACE VIEW `evacuation_center_stats` AS
SELECT 
    ec.id,
    ec.name,
    ec.barangay_id,
    b.name as barangay_name,
    ec.capacity,
    ec.current_occupancy,
    (ec.capacity - ec.current_occupancy) as available_capacity,
    ROUND((ec.current_occupancy / ec.capacity) * 100, 2) as utilization_percentage,
    ec.contact_person,
    ec.contact_number,
    ec.is_active,
    COUNT(h.id) as assigned_households,
    COUNT(r.id) as total_residents
FROM evacuation_centers ec
LEFT JOIN barangays b ON ec.barangay_id = b.id
LEFT JOIN households h ON h.assigned_evacuation_center = ec.name AND h.barangay_id = ec.barangay_id
LEFT JOIN residents r ON r.household_id = h.id AND r.is_active = 1
GROUP BY ec.id, ec.name, ec.barangay_id, b.name, ec.capacity, ec.current_occupancy, ec.contact_person, ec.contact_number, ec.is_active;

-- View for household evacuation assignments
CREATE OR REPLACE VIEW `household_evacuation_assignments` AS
SELECT 
    h.id as household_id,
    h.household_head,
    h.address,
    h.barangay_id,
    b.name as barangay_name,
    h.assigned_evacuation_center,
    h.collection_point,
    h.evacuation_vehicle,
    h.vehicle_driver,
    h.control_number,
    COUNT(r.id) as member_count,
    SUM(CASE WHEN r.has_special_needs = 1 THEN 1 ELSE 0 END) as special_needs_count
FROM households h
LEFT JOIN barangays b ON h.barangay_id = b.id
LEFT JOIN residents r ON r.household_id = h.id AND r.is_active = 1
GROUP BY h.id, h.household_head, h.address, h.barangay_id, b.name, h.assigned_evacuation_center, h.collection_point, h.evacuation_vehicle, h.vehicle_driver, h.control_number;

-- =====================================================
-- CREATE TRIGGERS FOR DATA INTEGRITY
-- =====================================================

-- Trigger to update evacuation center occupancy when household assignment changes
DELIMITER //
CREATE TRIGGER `update_evacuation_center_occupancy_insert` 
AFTER INSERT ON `households`
FOR EACH ROW
BEGIN
    IF NEW.assigned_evacuation_center IS NOT NULL AND NEW.assigned_evacuation_center != '' THEN
        UPDATE evacuation_centers 
        SET current_occupancy = current_occupancy + (
            SELECT COUNT(*) FROM residents 
            WHERE household_id = NEW.id AND is_active = 1
        )
        WHERE name = NEW.assigned_evacuation_center AND barangay_id = NEW.barangay_id;
    END IF;
END//

CREATE TRIGGER `update_evacuation_center_occupancy_update` 
AFTER UPDATE ON `households`
FOR EACH ROW
BEGIN
    -- Remove from old evacuation center
    IF OLD.assigned_evacuation_center IS NOT NULL AND OLD.assigned_evacuation_center != '' THEN
        UPDATE evacuation_centers 
        SET current_occupancy = current_occupancy - (
            SELECT COUNT(*) FROM residents 
            WHERE household_id = OLD.id AND is_active = 1
        )
        WHERE name = OLD.assigned_evacuation_center AND barangay_id = OLD.barangay_id;
    END IF;
    
    -- Add to new evacuation center
    IF NEW.assigned_evacuation_center IS NOT NULL AND NEW.assigned_evacuation_center != '' THEN
        UPDATE evacuation_centers 
        SET current_occupancy = current_occupancy + (
            SELECT COUNT(*) FROM residents 
            WHERE household_id = NEW.id AND is_active = 1
        )
        WHERE name = NEW.assigned_evacuation_center AND barangay_id = NEW.barangay_id;
    END IF;
END//

-- Trigger to update evacuation center occupancy when residents are added/removed
CREATE TRIGGER `update_evacuation_center_occupancy_resident_insert` 
AFTER INSERT ON `residents`
FOR EACH ROW
BEGIN
    IF NEW.is_active = 1 THEN
        UPDATE evacuation_centers ec
        JOIN households h ON h.id = NEW.household_id
        SET ec.current_occupancy = ec.current_occupancy + 1
        WHERE ec.name = h.assigned_evacuation_center AND ec.barangay_id = h.barangay_id;
    END IF;
END//

CREATE TRIGGER `update_evacuation_center_occupancy_resident_update` 
AFTER UPDATE ON `residents`
FOR EACH ROW
BEGIN
    -- If resident status changed from active to inactive
    IF OLD.is_active = 1 AND NEW.is_active = 0 THEN
        UPDATE evacuation_centers ec
        JOIN households h ON h.id = NEW.household_id
        SET ec.current_occupancy = ec.current_occupancy - 1
        WHERE ec.name = h.assigned_evacuation_center AND ec.barangay_id = h.barangay_id;
    END IF;
    
    -- If resident status changed from inactive to active
    IF OLD.is_active = 0 AND NEW.is_active = 1 THEN
        UPDATE evacuation_centers ec
        JOIN households h ON h.id = NEW.household_id
        SET ec.current_occupancy = ec.current_occupancy + 1
        WHERE ec.name = h.assigned_evacuation_center AND ec.barangay_id = h.barangay_id;
    END IF;
END//
DELIMITER ;

-- =====================================================
-- CREATE INDEXES FOR PERFORMANCE
-- =====================================================

-- Composite indexes for better query performance
CREATE INDEX `idx_households_barangay_evac` ON `households` (`barangay_id`, `assigned_evacuation_center`);
CREATE INDEX `idx_residents_household_active` ON `residents` (`household_id`, `is_active`);
CREATE INDEX `idx_evacuation_centers_barangay_active` ON `evacuation_centers` (`barangay_id`, `is_active`);
CREATE INDEX `idx_id_cards_resident_status` ON `id_cards` (`resident_id`, `status`);

-- =====================================================
-- GRANT PERMISSIONS (if needed)
-- =====================================================
-- Uncomment and modify the following lines if you need to grant specific permissions
-- GRANT SELECT, INSERT, UPDATE, DELETE ON `resident_management`.* TO 'your_username'@'localhost';
-- FLUSH PRIVILEGES;

-- =====================================================
-- SCHEMA COMPLETION MESSAGE
-- =====================================================
SELECT 'Canlaon Evacuee System Database Schema Created Successfully!' as message;
SELECT COUNT(*) as total_tables FROM information_schema.tables WHERE table_schema = 'resident_management'; 