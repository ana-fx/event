package main

import (
	"database/sql"
	"event-backend/internal/database"
	"event-backend/internal/models"
	"fmt"
	"log"
	"os"
	"time"

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

	// 5. Create Admin User
	log.Println("Creating admin user...")
	adminPassword, _ := bcrypt.GenerateFromPassword([]byte("admin@admin.com"), bcrypt.DefaultCost)
	admin := &models.User{
		Name:     "Super Admin",
		Email:    "admin@admin.com",
		Password: string(adminPassword),
		Role:     "admin",
		IsActive: true,
		Username: sql.NullString{String: "admin", Valid: true},
	}
	if err := models.CreateUser(admin); err != nil {
		log.Fatal("Failed to create admin user:", err)
	}

	// 6. Create Organizer User
	log.Println("Creating organizer user...")
	orgPassword, _ := bcrypt.GenerateFromPassword([]byte("organizer@test.com"), bcrypt.DefaultCost)
	organizer := &models.User{
		Name:          "Global Events Organizer",
		Email:         "organizer@test.com",
		Password:      string(orgPassword),
		Role:          "organizer",
		IsActive:      true,
		Username:      sql.NullString{String: "global_org", Valid: true},
		OrganizerName: sql.NullString{String: "Global Events Inc.", Valid: true},
		AboutUs:       sql.NullString{String: "Leading organizer of international festivals, tech expos, and food carnivals worldwide.", Valid: true},
		Phone:         sql.NullString{String: "081234567890", Valid: true},
		Province:      sql.NullString{String: "DKI Jakarta", Valid: true},
		City:          sql.NullString{String: "Jakarta Selatan", Valid: true},
		ZipCode:       sql.NullString{String: "12345", Valid: true},
		Address:       sql.NullString{String: "Sudirman Central Business District (SCBD), Jakarta", Valid: true},
	}
	if err := models.CreateUser(organizer); err != nil {
		log.Fatal("Failed to create organizer user:", err)
	}

	// 7. Create Sample Events
	log.Println("Creating sample events...")
	events := []struct {
		Name     string
		Slug     string
		Category string
		City     string
	}{
		{"Jakarta Jazz Festival 2026", "jakarta-jazz-2026", "Music", "Jakarta"},
		{"Tech Expo Asia 2026", "tech-expo-2026", "Technology", "Bandung"},
		{"International Food Carnival", "food-carnival-2026", "Food", "Surabaya"},
	}

	for _, eData := range events {
		event := &models.Event{
			Name:          eData.Name,
			Slug:          eData.Slug,
			Category:      eData.Category,
			Status:        "published",
			StartDate:     time.Now().AddDate(0, 1, 0),
			EndDate:       time.Now().AddDate(0, 1, 1),
			Description:   fmt.Sprintf("Experience the biggest %s event of the year! Join thousands of enthusiasts at the %s in %s.", eData.Category, eData.Name, eData.City),
			Terms:         stringPtr("1. Non-refundable ticket. 2. Must bring valid ID. 3. Follow all event protocols."),
			Location:      stringPtr("Grand Convention Hall"),
			City:          stringPtr(eData.City),
			Province:      stringPtr("Jawa"),
			OrganizerID:   &organizer.ID,
			OrganizerName: stringPtr(organizer.OrganizerName.String),

			// Financial Settings
			AdminFee:         5000,
			AdminFeeType:     "fixed",
			PPN:              11,
			PPNType:          "percent",
			OrganizerTax:     5,
			OrganizerTaxType: "percent",
			PgFee:            2500,
			PgFeeType:        "fixed",
			PgFeeBank:        4440,
			PgFeeQris:        0.7,

			ResellerFeeType:          "fixed",
			ResellerFeeValue:         10000,
			OrganizerFeeOnlineType:   "percent",
			OrganizerFeeOnline:       2.5,
			OrganizerFeeResellerType: "fixed",
			OrganizerFeeReseller:     1500,
		}

		if err := models.CreateEvent(event); err != nil {
			log.Fatalf("Failed to create event %s: %v", event.Slug, err)
		}

		// 8. Create Tickets for each event
		tickets := []struct {
			Name  string
			Price float64
			Quota int
		}{
			{"Early Bird", 150000, 100},
			{"General Admission", 250000, 500},
			{"VIP", 750000, 50},
		}

		for _, tData := range tickets {
			ticket := &models.Ticket{
				EventID:            event.ID,
				Name:               tData.Name,
				Price:              tData.Price,
				Quota:              tData.Quota,
				MaxPurchasePerUser: 4,
				IsActive:           true,
				StartDate:          time.Now(),
				EndDate:            event.StartDate,
				Description:        sql.NullString{String: fmt.Sprintf("Access to %s", tData.Name), Valid: true},
			}
			if err := models.CreateTicket(ticket); err != nil {
				log.Fatalf("Failed to create ticket %s: %v", ticket.Name, err)
			}
		}
		log.Printf("Created event: %s with tickets", event.Name)
	}

	// 9. Create Banner
	log.Println("Creating sample banner...")
	banner := &models.Banner{
		Slug:      "jazz-fest-2026",
		Title:     "Jakarta Jazz Festival 2026",
		ImagePath: "/uploads/events/1770321313086990300.webp",
		LinkURL:   "/events/jakarta-jazz-2026",
		IsActive:  true,
	}
	if err := models.CreateBanner(banner); err != nil {
		log.Printf("Warning: failed to create banner: %v", err)
	}

	log.Println("Database reset and seeding successful!")
	log.Println("Admin: admin@admin.com / admin@admin.com")
	log.Println("Organizer: organizer@test.com / organizer@test.com")
}

func stringPtr(s string) *string {
	return &s
}
