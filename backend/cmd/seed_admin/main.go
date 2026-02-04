package main

import (
	"event-backend/internal/database"
	"event-backend/internal/models"
	"fmt"
	"os"

	"github.com/joho/godotenv"
	"golang.org/x/crypto/bcrypt"
)

func main() {
	// Load .env from backend root (assuming running from backend dir)
	if err := godotenv.Load(); err != nil {
		fmt.Println("Warning: .env file not found")
	}

	connStr := os.Getenv("DATABASE_URL")
	if connStr == "" {
		connStr = "postgres://postgres:root@localhost:5432/event_db?sslmode=disable"
	}
	database.Connect(connStr)

	password := "123456"
	hashed, err := bcrypt.GenerateFromPassword([]byte(password), bcrypt.DefaultCost)
	if err != nil {
		panic(err)
	}

	user := &models.User{
		Name:     "Admin User",
		Email:    "admin@admin.com",
		Password: string(hashed),
		Role:     "admin",
		IsActive: true,
	}

	// Check if exists
	existing, _ := models.GetUserByEmail(user.Email)
	if existing != nil {
		fmt.Println("Admin user exists, updating password...")
		user.ID = existing.ID
		if err := models.UpdateUser(user); err != nil {
			panic(err)
		}
		fmt.Println("Admin password updated to 123456")
		return
	}

	if err := models.CreateUser(user); err != nil {
		panic(err)
	}
	fmt.Printf("Admin user created!\nEmail: %s\nPassword: %s\n", user.Email, password)
}
