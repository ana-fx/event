package main

import (
	"database/sql"
	"event-backend/internal/database"
	"event-backend/internal/models"
	"log"
	"os"
	"time"

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
		"withdrawals", "reseller_deposits", "event_reseller", "settings",
		"banners", "event_scanner", "contacts", "transactions", "tickets", "events", "users",
	}

	for _, table := range tables {
		_, err := database.DB.Exec("TRUNCATE TABLE " + table + " RESTART IDENTITY CASCADE")
		if err != nil {
			log.Printf("Warning: failed to truncate %s: %v", table, err)
		}
	}

	// 4. Create Admin User
	log.Println("Creating admin user...")
	hashedPassword, _ := bcrypt.GenerateFromPassword([]byte("admin@admin.com"), bcrypt.DefaultCost)
	admin := &models.User{
		Name:     "Admin Ingate",
		Email:    "admin@admin.com",
		Password: string(hashedPassword),
		Role:     "admin",
		IsActive: true,
	}
	models.CreateUser(admin)

	// 5. Create Real Event
	log.Println("Creating real event...")
	bannerPath := "/uploads/events/1770321313086990300.webp"
	thumbPath := "/uploads/events/1770321313087489500.png"

	event := &models.Event{
		Name:           "Jakarta International Jazz Festival 2026",
		Slug:           "jakarta-jazz-2026",
		Category:       "Music",
		Status:         "published",
		BannerPath:     &bannerPath,
		ThumbnailPath:  &thumbPath,
		StartDate:      time.Now().AddDate(0, 2, 0), // 2 months from now
		EndDate:        time.Now().AddDate(0, 2, 2), // 2 months + 2 days
		Description:    "The biggest jazz festival in Southeast Asia is back. Featuring world-class musicians and local legends across 10 different stages.",
		Location:       stringPtr("JIEXPO Kemayoran"),
		City:           stringPtr("Jakarta"),
		OrganizerName:  stringPtr("Ingate Promotions"),
		SeoTitle:       stringPtr("Jakarta Jazz Festival 2026 | Buy Tickets"),
		SeoDescription: stringPtr("Get your tickets for Jakarta Jazz Festival 2026. Join the best musical experience in the heart of Jakarta."),
	}
	err := models.CreateEvent(event)
	if err != nil {
		log.Fatal("Failed to create event:", err)
	}

	// 6. Create Tickets
	log.Println("Creating tickets...")
	ticketCategories := []struct {
		Name  string
		Price float64
		Quota int
	}{
		{"Early Bird - 3 Day Pass", 1500000, 500},
		{"Presale 1 - Daily Pass", 650000, 1000},
		{"VIP Experience", 4500000, 200},
	}

	for _, tc := range ticketCategories {
		t := &models.Ticket{
			EventID:            event.ID,
			Name:               tc.Name,
			Description:        sql.NullString{String: "Enjoy the best spots and exclusive lounge access.", Valid: true},
			Price:              tc.Price,
			Quota:              tc.Quota,
			MaxPurchasePerUser: 4,
			IsActive:           true,
			StartDate:          time.Now(),
			EndDate:            event.StartDate,
		}
		models.CreateTicket(t)
	}

	// 7. Create Banner
	log.Println("Creating homepage banner...")
	banner := &models.Banner{
		Slug:      "main-jazz-fest",
		Title:     "Jakarta Jazz Festival 2026",
		ImagePath: bannerPath,
		LinkURL:   "/events/jakarta-jazz-2026",
		IsActive:  true,
	}
	models.CreateBanner(banner)

	log.Println("Seeding completed successfully!")
}

func stringPtr(s string) *string {
	return &s
}
