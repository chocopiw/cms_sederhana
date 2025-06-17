-- Update existing database to add email field
USE cms_sederhana;

-- Add email column to users table if it doesn't exist
ALTER TABLE users ADD COLUMN IF NOT EXISTS email VARCHAR(100) NOT NULL UNIQUE AFTER username;

-- Update existing admin user with email if email is empty
UPDATE users SET email = 'admin@example.com' WHERE username = 'admin' AND (email IS NULL OR email = ''); 