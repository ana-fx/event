-- +goose Up
-- +goose StatementBegin
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'user' NOT NULL, -- using VARCHAR instead of ENUM for flexibility, with check if needed
    profile_photo_path VARCHAR(2048) NULL,
    phone VARCHAR(255) NULL,
    address TEXT NULL,
    bio TEXT NULL,
    balance DECIMAL(16, 2) DEFAULT 0 NOT NULL,
    is_active BOOLEAN DEFAULT TRUE NOT NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE events (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    category VARCHAR(255) NOT NULL,
    status VARCHAR(50) DEFAULT 'draft' NOT NULL,
    banner_path VARCHAR(255) NULL,
    thumbnail_path VARCHAR(255) NULL,
    start_date TIMESTAMP NOT NULL,
    end_date TIMESTAMP NOT NULL,
    description TEXT NOT NULL,
    terms TEXT NULL,
    location VARCHAR(255) NULL,
    province VARCHAR(255) NULL,
    city VARCHAR(255) NULL,
    zip VARCHAR(255) NULL,
    google_map_embed TEXT NULL,
    seo_title VARCHAR(255) NULL,
    seo_description TEXT NULL,
    organizer_name VARCHAR(255) NULL,
    organizer_logo_path VARCHAR(255) NULL,
    reseller_fee_type VARCHAR(50) DEFAULT 'fixed' NOT NULL,
    reseller_fee_value DECIMAL(12, 2) DEFAULT 0 NOT NULL,
    organizer_fee_online_type VARCHAR(50) DEFAULT 'fixed' NOT NULL,
    organizer_fee_online DECIMAL(12, 2) DEFAULT 0 NOT NULL,
    organizer_fee_reseller_type VARCHAR(50) DEFAULT 'fixed' NOT NULL,
    organizer_fee_reseller DECIMAL(12, 2) DEFAULT 0 NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE tickets (
    id SERIAL PRIMARY KEY,
    event_id INTEGER REFERENCES events(id) ON DELETE CASCADE,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(12, 2) DEFAULT 0 NOT NULL,
    quota INTEGER NOT NULL,
    max_purchase_per_user INTEGER NOT NULL,
    start_date TIMESTAMP NOT NULL,
    end_date TIMESTAMP NOT NULL,
    description TEXT NULL,
    is_active BOOLEAN DEFAULT TRUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE transactions (
    id SERIAL PRIMARY KEY,
    code VARCHAR(255) UNIQUE NOT NULL,
    event_id INTEGER REFERENCES events(id) ON DELETE CASCADE,
    ticket_id INTEGER REFERENCES tickets(id) ON DELETE CASCADE,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(255) NOT NULL,
    city VARCHAR(255) NOT NULL,
    nik VARCHAR(255) NOT NULL,
    gender VARCHAR(20) NOT NULL,
    quantity INTEGER NOT NULL,
    total_price DECIMAL(15, 2) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending' NOT NULL,
    redeemed_at TIMESTAMP NULL,
    redeemed_by INTEGER REFERENCES users(id) ON DELETE SET NULL,
    reseller_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
    snap_token VARCHAR(255) NULL,
    payment_type VARCHAR(255) NULL,
    midtrans_transaction_id VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE contacts (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE event_scanner (
    id SERIAL PRIMARY KEY,
    event_id INTEGER REFERENCES events(id) ON DELETE CASCADE,
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE banners (
    id SERIAL PRIMARY KEY,
    slug VARCHAR(255) UNIQUE NOT NULL,
    title VARCHAR(255) NULL,
    image_path VARCHAR(255) NOT NULL,
    link_url VARCHAR(255) NULL,
    is_active BOOLEAN DEFAULT TRUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE settings (
    id SERIAL PRIMARY KEY,
    key VARCHAR(255) UNIQUE NOT NULL,
    value TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE event_reseller (
    id SERIAL PRIMARY KEY,
    event_id INTEGER REFERENCES events(id) ON DELETE CASCADE,
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    commission_type VARCHAR(50) DEFAULT 'fixed' NOT NULL,
    commission_value DECIMAL(12, 2) DEFAULT 0 NOT NULL,
    organizer_fee DECIMAL(12, 2) DEFAULT 0 NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE reseller_deposits (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    amount DECIMAL(16, 2) NOT NULL,
    note TEXT NULL,
    created_by INTEGER REFERENCES users(id) ON DELETE SET NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE withdrawals (
    id SERIAL PRIMARY KEY,
    event_id INTEGER REFERENCES events(id) ON DELETE CASCADE,
    amount DECIMAL(15, 2) NOT NULL,
    reference VARCHAR(255) NOT NULL,
    note TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- +goose StatementEnd

-- +goose Down
-- +goose StatementBegin
DROP TABLE IF EXISTS withdrawals;
DROP TABLE IF EXISTS reseller_deposits;
DROP TABLE IF EXISTS event_reseller;
DROP TABLE IF EXISTS settings;
DROP TABLE IF EXISTS banners;
DROP TABLE IF EXISTS event_scanner;
DROP TABLE IF EXISTS contacts;
DROP TABLE IF EXISTS transactions;
DROP TABLE IF EXISTS tickets;
DROP TABLE IF EXISTS events;
DROP TABLE IF EXISTS users;
-- +goose StatementEnd
