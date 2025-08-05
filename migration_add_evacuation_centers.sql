-- =====================================================
-- MIGRATION: ADD EVACUATION CENTERS TABLE
-- =====================================================
-- This migration adds the evacuation centers functionality
-- to an existing Canlaon Evacuee System database.
-- =====================================================

USE `resident_management`;

-- =====================================================
-- ADD EVACUATION CENTERS TABLE
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
-- ADD EVACUATION CENTER ASSIGNMENT COLUMN TO HOUSEHOLDS
-- =====================================================
-- Add the assigned_evacuation_center column if it doesn't exist
ALTER TABLE `households` 
ADD COLUMN IF NOT EXISTS `assigned_evacuation_center` VARCHAR(150) NULL AFTER `vehicle_driver`,
ADD INDEX IF NOT EXISTS `idx_assigned_evacuation_center` (`assigned_evacuation_center`),
ADD INDEX IF NOT EXISTS `idx_households_barangay_evac` (`barangay_id`, `assigned_evacuation_center`);

-- =====================================================
-- INSERT DEFAULT EVACUATION CENTERS
-- =====================================================
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
('Barangay 10 Sports Complex', 10, 'Barangay 10, Canlaon City, Negros Oriental', 350, 'Sports Coordinator', '09123456798')
ON DUPLICATE KEY UPDATE 
    `address` = VALUES(`address`),
    `capacity` = VALUES(`capacity`),
    `contact_person` = VALUES(`contact_person`),
    `contact_number` = VALUES(`contact_number`);

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

-- Drop existing triggers if they exist
DROP TRIGGER IF EXISTS `update_evacuation_center_occupancy_insert`;
DROP TRIGGER IF EXISTS `update_evacuation_center_occupancy_update`;
DROP TRIGGER IF EXISTS `update_evacuation_center_occupancy_resident_insert`;
DROP TRIGGER IF EXISTS `update_evacuation_center_occupancy_resident_update`;

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
-- UPDATE EXISTING HOUSEHOLDS WITH EVACUATION CENTERS
-- =====================================================
-- This will automatically assign existing households to evacuation centers
-- based on their barangay assignment

UPDATE households h
JOIN evacuation_centers ec ON h.barangay_id = ec.barangay_id
SET h.assigned_evacuation_center = ec.name
WHERE h.assigned_evacuation_center IS NULL 
AND ec.is_active = 1
AND ec.current_occupancy < ec.capacity;

-- =====================================================
-- MIGRATION COMPLETION MESSAGE
-- =====================================================
SELECT 'Evacuation Centers Migration Completed Successfully!' as message;
SELECT COUNT(*) as evacuation_centers_count FROM evacuation_centers;
SELECT COUNT(*) as households_with_evacuation_centers FROM households WHERE assigned_evacuation_center IS NOT NULL; 