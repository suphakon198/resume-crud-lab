-- WEEK 3: Run on existing database after previous week's CRUD Lab.
-- This script is NON-DESTRUCTIVE. Do not rerun the old setup.sql (it DROPS data).
-- Select your existing resume database in phpMyAdmin first.
CREATE TABLE IF NOT EXISTS users (
 id INT AUTO_INCREMENT PRIMARY KEY,
 profile_id INT NOT NULL UNIQUE,
 email VARCHAR(190) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_users_profile FOREIGN KEY (profile_id) REFERENCES profile(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
