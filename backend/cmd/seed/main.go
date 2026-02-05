package main

import (
	"database/sql"
	"fmt"
	"log"
	"os"
	"time"

	"github.com/joho/godotenv"
	_ "github.com/lib/pq"
	"golang.org/x/crypto/bcrypt"
)

func main() {
	// 1. Load .env
	if err := godotenv.Load(); err != nil {
		log.Println("No .env file found, trying default or system env")
	}

	connStr := os.Getenv("DATABASE_URL")
	if connStr == "" {
		connStr = "postgres://postgres:root@localhost:5432/event_app_db?sslmode=disable"
	}

	// 2. Connect DB
	db, err := sql.Open("postgres", connStr)
	if err != nil {
		log.Fatal("Failed to connect:", err)
	}
	defer db.Close()

	if err := db.Ping(); err != nil {
		log.Fatal("Failed to ping DB:", err)
	}

	// 3. Define Admin User
	email := "admin@ingate.com"
	password := "password"
	name := "Super Admin"

	// 4. Hash Password
	hashedBytes, err := bcrypt.GenerateFromPassword([]byte(password), bcrypt.DefaultCost)
	if err != nil {
		log.Fatal("Failed to hash password:", err)
	}
	hashedPassword := string(hashedBytes)

	// 5. Insert User
	query := `
		INSERT INTO users (name, email, password, role, is_active, created_at, updated_at)
		VALUES ($1, $2, $3, 'admin', true, $4, $5)
		ON CONFLICT (email) DO UPDATE 
		SET role='admin', password=$3, is_active=true, updated_at=$5
		RETURNING id
	`

	var id int
	err = db.QueryRow(query, name, email, hashedPassword, time.Now(), time.Now()).Scan(&id)
	if err != nil {
		log.Fatal("Failed to insert admin:", err)
	}

	fmt.Printf("Admin user created/updated successfully!\nID: %d\nEmail: %s\nPassword: %s\n", id, email, password)
}
