-- +goose Up
-- +goose StatementBegin
-- Fix users table
ALTER TABLE users ADD COLUMN IF NOT EXISTS username VARCHAR(255) UNIQUE NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS email_verified_at TIMESTAMP NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS profile_photo_path VARCHAR(2048) NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS organizer_name VARCHAR(255) NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS about_us TEXT NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS phone VARCHAR(255) NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS address TEXT NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS province VARCHAR(255) NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS city VARCHAR(255) NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS zip_code VARCHAR(50) NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS bio TEXT NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS balance DECIMAL(16, 2) DEFAULT 0 NOT NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS remember_token VARCHAR(100) NULL;

-- Fix events table
ALTER TABLE events ADD COLUMN IF NOT EXISTS organizer_id INTEGER REFERENCES users(id) NULL;

-- Fix transactions table
ALTER TABLE transactions ADD COLUMN IF NOT EXISTS payment_type VARCHAR(255) NULL;
ALTER TABLE transactions ADD COLUMN IF NOT EXISTS midtrans_transaction_id VARCHAR(255) NULL;
ALTER TABLE transactions ADD COLUMN IF NOT EXISTS redeemed_at TIMESTAMP NULL;
ALTER TABLE transactions ADD COLUMN IF NOT EXISTS redeemed_by INTEGER REFERENCES users(id) ON DELETE SET NULL;
ALTER TABLE transactions ADD COLUMN IF NOT EXISTS reseller_id INTEGER REFERENCES users(id) ON DELETE SET NULL;
-- +goose StatementEnd

-- +goose Down
-- +goose StatementBegin
-- We don't necessarily want to drop columns in downgrade if they were supposed to be there, 
-- but for completeness of migration:
ALTER TABLE transactions DROP COLUMN IF EXISTS payment_type;
ALTER TABLE transactions DROP COLUMN IF EXISTS midtrans_transaction_id;
ALTER TABLE transactions DROP COLUMN IF EXISTS redeemed_at;
ALTER TABLE transactions DROP COLUMN IF EXISTS redeemed_by;
ALTER TABLE transactions DROP COLUMN IF EXISTS reseller_id;

ALTER TABLE events DROP COLUMN IF EXISTS organizer_id;

ALTER TABLE users DROP COLUMN IF EXISTS username;
ALTER TABLE users DROP COLUMN IF EXISTS email_verified_at;
ALTER TABLE users DROP COLUMN IF EXISTS profile_photo_path;
ALTER TABLE users DROP COLUMN IF EXISTS organizer_name;
ALTER TABLE users DROP COLUMN IF EXISTS about_us;
ALTER TABLE users DROP COLUMN IF EXISTS phone;
ALTER TABLE users DROP COLUMN IF EXISTS address;
ALTER TABLE users DROP COLUMN IF EXISTS province;
ALTER TABLE users DROP COLUMN IF EXISTS city;
ALTER TABLE users DROP COLUMN IF EXISTS zip_code;
ALTER TABLE users DROP COLUMN IF EXISTS bio;
ALTER TABLE users DROP COLUMN IF EXISTS balance;
ALTER TABLE users DROP COLUMN IF EXISTS remember_token;
-- +goose StatementEnd
