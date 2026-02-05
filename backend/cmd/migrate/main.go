package main

import (
	"database/sql"
	"log"
	"os"

	"github.com/joho/godotenv"
	_ "github.com/lib/pq"
)

func main() {
	if err := godotenv.Load(); err != nil {
		log.Println("No .env file found")
	}

	connStr := os.Getenv("DATABASE_URL")
	if connStr == "" {
		connStr = "postgres://postgres:root@localhost:5432/event_app_db?sslmode=disable"
	}

	db, err := sql.Open("postgres", connStr)
	if err != nil {
		log.Fatal(err)
	}
	defer db.Close()

	if err := db.Ping(); err != nil {
		log.Fatal(err)
	}

	// Schema creation
	queries := []string{
		`CREATE TABLE IF NOT EXISTS users (
			id SERIAL PRIMARY KEY,
			name VARCHAR(255) NOT NULL,
			email VARCHAR(255) UNIQUE NOT NULL,
			password VARCHAR(255) NOT NULL,
			role VARCHAR(50) NOT NULL DEFAULT 'user',
			is_active BOOLEAN DEFAULT TRUE,
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			deleted_at TIMESTAMP
		);`,
		`CREATE TABLE IF NOT EXISTS events (
			id SERIAL PRIMARY KEY,
			name VARCHAR(255) NOT NULL,
			slug VARCHAR(255) UNIQUE NOT NULL,
			category VARCHAR(100),
			status VARCHAR(50) DEFAULT 'draft',
			description TEXT,
			terms TEXT,
			location VARCHAR(255),
			province VARCHAR(100),
			city VARCHAR(100),
			zip VARCHAR(20),
			google_map_embed TEXT,
			seo_title VARCHAR(255),
			seo_description TEXT,
			organizer_name VARCHAR(255),
			banner_path VARCHAR(255),
			thumbnail_path VARCHAR(255),
			organizer_logo_path VARCHAR(255),
			reseller_fee_type VARCHAR(50) DEFAULT 'fixed',
			reseller_fee_value DECIMAL(15, 2) DEFAULT 0,
			organizer_fee_online_type VARCHAR(50) DEFAULT 'fixed',
			organizer_fee_online DECIMAL(15, 2) DEFAULT 0,
			organizer_fee_reseller_type VARCHAR(50) DEFAULT 'fixed',
			organizer_fee_reseller DECIMAL(15, 2) DEFAULT 0,
			start_date TIMESTAMP,
			end_date TIMESTAMP,
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			deleted_at TIMESTAMP
		);`,
		`CREATE TABLE IF NOT EXISTS tickets (
			id SERIAL PRIMARY KEY,
			event_id INT NOT NULL REFERENCES events(id),
			name VARCHAR(255) NOT NULL,
			price DECIMAL(15, 2) NOT NULL DEFAULT 0,
			quota INT NOT NULL DEFAULT 0,
			max_purchase_per_user INT DEFAULT 5,
			description TEXT,
			is_active BOOLEAN DEFAULT TRUE,
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			deleted_at TIMESTAMP,
			start_date TIMESTAMP,
			end_date TIMESTAMP
		);`,
		`CREATE TABLE IF NOT EXISTS transactions (
			id SERIAL PRIMARY KEY,
			code VARCHAR(100) UNIQUE NOT NULL,
			event_id INT REFERENCES events(id),
			ticket_id INT REFERENCES tickets(id),
			user_id INT REFERENCES users(id),
			name VARCHAR(255),
			email VARCHAR(255),
			phone VARCHAR(50),
			payment_method VARCHAR(50),
			status VARCHAR(50) DEFAULT 'pending',
			quantity INT NOT NULL,
			total_price DECIMAL(15, 2) NOT NULL,
			snap_token VARCHAR(255),
			redirect_url TEXT,
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			deleted_at TIMESTAMP
		);`,
		`CREATE TABLE IF NOT EXISTS event_scanner (
			id SERIAL PRIMARY KEY,
			event_id INT NOT NULL REFERENCES events(id) ON DELETE CASCADE,
			user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			UNIQUE(event_id, user_id)
		);`,
		`CREATE TABLE IF NOT EXISTS event_reseller (
			id SERIAL PRIMARY KEY,
			event_id INT NOT NULL REFERENCES events(id) ON DELETE CASCADE,
			user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
			commission_type VARCHAR(50) DEFAULT 'fixed',
			commission_value DECIMAL(15, 2) DEFAULT 0,
			organizer_fee DECIMAL(15, 2) DEFAULT 0,
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			UNIQUE(event_id, user_id)
		);`,
		`CREATE TABLE IF NOT EXISTS banners (
			id SERIAL PRIMARY KEY,
			slug VARCHAR(255) UNIQUE NOT NULL,
			title VARCHAR(255) NOT NULL,
			image_path VARCHAR(255) NOT NULL,
			link_url VARCHAR(255),
			is_active BOOLEAN DEFAULT TRUE,
			sort_order INT DEFAULT 0,
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			deleted_at TIMESTAMP
		);`,
	}

	for _, q := range queries {
		if _, err := db.Exec(q); err != nil {
			log.Fatalf("Failed to execute query: %s\nError: %v", q, err)
		}
	}

	log.Println("Migration completed successfully!")
}
