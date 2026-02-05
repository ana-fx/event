package main

import (
	"database/sql"
	"fmt"
	"log"
	"os"

	"github.com/joho/godotenv"
	_ "github.com/lib/pq"
	"golang.org/x/crypto/bcrypt"
)

func main() {
	// Load .env
	godotenv.Load()

	connStr := os.Getenv("DATABASE_URL")
	if connStr == "" {
		connStr = "postgres://postgres:root@localhost:5432/event_app_db?sslmode=disable"
	}

	db, err := sql.Open("postgres", connStr)
	if err != nil {
		log.Fatal(err)
	}
	defer db.Close()

	email := "admin@ingate.com"
	password := "password"

	var active bool
	var storedHash string
	var role string

	// Get user
	err = db.QueryRow("SELECT password, role, is_active FROM users WHERE email = $1", email).Scan(&storedHash, &role, &active)
	if err != nil {
		log.Fatalf("User %s not found: %v", email, err)
	}

	fmt.Printf("User Found: %s\nRole: %s\nActive: %v\n", email, role, active)

	// Compare Hash
	err = bcrypt.CompareHashAndPassword([]byte(storedHash), []byte(password))
	if err != nil {
		fmt.Printf("❌ Password verification FAILED: %v\n", err)
	} else {
		fmt.Printf("✅ Password verification PASSED\n")
	}

	// Also check anntix user just in case
	email2 := "admin@anntix.com"
	err = db.QueryRow("SELECT password, role, is_active FROM users WHERE email = $1", email2).Scan(&storedHash, &role, &active)
	if err == nil {
		fmt.Printf("\nChecking %s...\n", email2)
		err = bcrypt.CompareHashAndPassword([]byte(storedHash), []byte(password))
		if err != nil {
			fmt.Printf("❌ Password verification FAILED: %v\n", err)
		} else {
			fmt.Printf("✅ Password verification PASSED\n")
		}
	}
}
