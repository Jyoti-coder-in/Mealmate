-- MealMate student College ID verification migration
-- BACK UP mealmate_db before running. Run once in phpMyAdmin > mealmate_db > Import.
ALTER TABLE users
  ADD COLUMN college_id_file VARCHAR(255) NULL,
  ADD COLUMN verification_status ENUM('Not submitted','Pending','Approved','Rejected') NOT NULL DEFAULT 'Not submitted',
  ADD COLUMN verification_note VARCHAR(500) NULL;

-- Vendor support (only run these if you have not already imported vendor_order_tracking_migration.sql)
-- CREATE TABLE IF NOT EXISTS vendors (
--  id INT AUTO_INCREMENT PRIMARY KEY, vendor_name VARCHAR(150) NOT NULL,
--  email VARCHAR(190) NOT NULL UNIQUE, password VARCHAR(255) NOT NULL,
--  phone VARCHAR(30) NULL, status ENUM('Active','Inactive') NOT NULL DEFAULT 'Active',
--  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- ALTER TABLE foods ADD COLUMN vendor_id INT NULL;
-- ALTER TABLE foods ADD COLUMN restaurant_id INT NULL;
