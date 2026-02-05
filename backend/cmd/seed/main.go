package main

import (
	"log"
	"os"

	"event-backend/internal/database"
	"event-backend/internal/models"

	"github.com/joho/godotenv"
	"golang.org/x/crypto/bcrypt"
)

func main() {
	// 1. Load .env
	if err := godotenv.Load(); err != nil {
		log.Println("No .env file found, using defaults")
	}

	// 2. Connect to Database
	connStr := os.Getenv("DATABASE_URL")
	if connStr == "" {
		connStr = "postgres://postgres:root@localhost:5432/event_db?sslmode=disable"
	}
	database.Connect(connStr)

	// 3. Reset Database (Truncate all tables)
	log.Println("Resetting database...")
	tables := []string{
		"withdrawals",
		"reseller_deposits",
		"event_reseller",
		"settings",
		"banners",
		"event_scanner",
		"contacts",
		"transactions",
		"tickets",
		"events",
		"users",
		"goose_db_version",
	}

	for _, table := range tables {
		_, err := database.DB.Exec("DROP TABLE IF EXISTS " + table + " CASCADE")
		if err != nil {
			log.Printf("Warning: failed to drop %s: %v", table, err)
		}
	}

	// 4. Run Migrations to recreate schema
	database.RunMigrations()

	// 5. Create Admin User
	log.Println("Creating admin user...")
	hashedPassword, err := bcrypt.GenerateFromPassword([]byte("admin@admin.com"), bcrypt.DefaultCost)
	if err != nil {
		log.Fatal("Failed to hash password:", err)
	}

	admin := &models.User{
		Name:     "Admin",
		Email:    "admin@admin.com",
		Password: string(hashedPassword),
		Role:     "admin",
		IsActive: true,
	}

	err = models.CreateUser(admin)
	if err != nil {
		log.Fatal("Failed to create admin user:", err)
	}

	log.Println("Database reset successful!")
	log.Println("Admin User Created:")
	log.Println("Email: admin@admin.com")
	log.Println("Password: admin@admin.com")
}
