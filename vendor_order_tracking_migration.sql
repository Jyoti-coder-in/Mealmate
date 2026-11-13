-- MealMate vendor and order tracking migration. Run once on mealmate_db.
CREATE TABLE IF NOT EXISTS vendors (
 id INT AUTO_INCREMENT PRIMARY KEY,
 vendor_name VARCHAR(150) NOT NULL,
 email VARCHAR(190) NOT NULL UNIQUE,
 password VARCHAR(255) NOT NULL,
 phone VARCHAR(30) NULL,
 status ENUM('Active','Inactive') NOT NULL DEFAULT 'Active',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Run these ALTER statements only once. If a column already exists, skip that line.
ALTER TABLE foods ADD COLUMN vendor_id INT NULL;
ALTER TABLE foods ADD COLUMN restaurant_id INT NULL;
CREATE INDEX idx_foods_vendor_id ON foods(vendor_id);
