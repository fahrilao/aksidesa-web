-- Create database if not exists
CREATE DATABASE IF NOT EXISTS aksidesa_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user and grant privileges
CREATE USER IF NOT EXISTS 'aksidesa_user'@'%' IDENTIFIED BY 'aksidesa_password';
GRANT ALL PRIVILEGES ON aksidesa_db.* TO 'aksidesa_user'@'%';
FLUSH PRIVILEGES;

-- Use the database
USE aksidesa_db;
