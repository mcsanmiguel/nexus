CREATE DATABASE IF NOT EXISTS nexustech_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nexustech_db;
CREATE TABLE IF NOT EXISTS products (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 sku VARCHAR(50) NOT NULL UNIQUE,
 name VARCHAR(120) NOT NULL,
 category VARCHAR(80) NOT NULL,
 price DECIMAL(12,2) NOT NULL DEFAULT 0,
 stock INT UNSIGNED NOT NULL DEFAULT 0,
 status ENUM('Active','Inactive') NOT NULL DEFAULT 'Active',
 description TEXT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
INSERT IGNORE INTO products(sku,name,category,price,stock,status,description) VALUES
('NX-ERP-001','NexusTech ERP Suite','ERP Software',89999,12,'Active','Finance, inventory and operations management.'),
('NX-HR-002','NexusTech HRIS & Payroll','HRIS & Payroll',45999,25,'Active','Employee records and payroll workflows.'),
('NX-POS-003','NexusTech POS Pro','POS System',29999,18,'Active','Point-of-sale, barcode scanning and branch reporting.');
