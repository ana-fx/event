package main

import (
	"log"
	"os"
	"time"

	"event-backend/internal/database"
	"event-backend/internal/models"

	"github.com/joho/godotenv"
	"golang.org/x/crypto/bcrypt"
)

func main() {
	// 1. Load .env
	err := godotenv.Load(".env")
	if err != nil {
		err = godotenv.Load("../.env") // Try parent if running from cmd/...
		if err != nil {
			log.Println("Warning: No .env file found, using defaults")
		}
	}

	// 2. Connect to Database
	connStr := os.Getenv("DATABASE_URL")
	if connStr == "" {
		connStr = "postgres://postgres:root@localhost:5432/event_app_db?sslmode=disable"
	}
	database.Connect(connStr)

	// 3. Reset Database (Drop and Recreate Public Schema)
	log.Println("Resetting database (Clean Slate)...")
	_, err = database.DB.Exec("DROP SCHEMA public CASCADE")
	if err != nil {
		log.Printf("Warning: failed to drop schema: %v", err)
	}
	_, err = database.DB.Exec("CREATE SCHEMA public")
	if err != nil {
		log.Fatal("Failed to recreate schema:", err)
	}
	_, err = database.DB.Exec("GRANT ALL ON SCHEMA public TO postgres")
	if err != nil {
		log.Printf("Warning: failed to grant permissions: %v", err)
	}
	_, err = database.DB.Exec("GRANT ALL ON SCHEMA public TO public")
	if err != nil {
		log.Printf("Warning: failed to grant permissions: %v", err)
	}

	// 4. Run Migrations to recreate schema
	database.RunMigrations()

	// Verify columns
	rows, err := database.DB.Query("SELECT column_name FROM information_schema.columns WHERE table_name = 'events'")
	if err == nil {
		log.Println("Columns in 'events' table:")
		for rows.Next() {
			var col string
			rows.Scan(&col)
			log.Printf("- %s", col)
		}
		rows.Close()
	}

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

	// 6. Create Sample Event
	log.Println("Creating sample event...")
	bannerPath := "/uploads/events/1770321313086990300.webp"
	thumbPath := "/uploads/events/1770321313087489500.png"

	event := &models.Event{
		Name:          "Jakarta International Jazz Festival 2026",
		Slug:          "jakarta-jazz-2026",
		Category:      "Music",
		Status:        "published",
		BannerPath:    &bannerPath,
		ThumbnailPath: &thumbPath,
		StartDate:     time.Now().AddDate(0, 2, 0),
		EndDate:       time.Now().AddDate(0, 2, 2),
		Description:   "<p>The biggest jazz festival in Southeast Asia is back. Featuring world-class musicians and local legends.</p>",
		Location:      stringPtr("JIEXPO Kemayoran"),
		City:          stringPtr("Jakarta"),
		OrganizerName: stringPtr("Ingate Promotions"),
	}

	err = models.CreateEvent(event)
	if err != nil {
		log.Fatal("Failed to create event:", err)
	}

	// 7. Create Tickets
	log.Println("Creating tickets...")
	tickets := []struct {
		Name  string
		Price float64
	}{
		{"General Admission", 500000},
		{"VIP Experience", 1500000},
	}

	for _, t := range tickets {
		ticket := &models.Ticket{
			EventID:            event.ID,
			Name:               t.Name,
			Price:              t.Price,
			Quota:              500,
			MaxPurchasePerUser: 4,
			IsActive:           true,
			StartDate:          time.Now(),
			EndDate:            event.StartDate,
		}
		models.CreateTicket(ticket)
	}

	// 8. Create Banner
	log.Println("Creating sample banner...")
	banner := &models.Banner{
		Slug:      "jazz-fest-2026",
		Title:     "Jakarta Jazz Festival 2026",
		ImagePath: bannerPath,
		LinkURL:   "/events/jakarta-jazz-2026",
		IsActive:  true,
	}
	err = models.CreateBanner(banner)
	if err != nil {
		log.Printf("Warning: failed to create banner: %v", err)
	}

	log.Println("Database reset and seeding successful!")
	log.Println("Admin: admin@admin.com / admin@admin.com")
}

func stringPtr(s string) *string {
	return &s
}
