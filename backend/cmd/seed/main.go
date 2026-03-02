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
	adminPassword, _ := bcrypt.GenerateFromPassword([]byte("admin@ingate.id"), bcrypt.DefaultCost)
	admin := &models.User{
		Name:     "Admin Ingate",
		Email:    "admin@ingate.id",
		Password: string(adminPassword),
		Role:     "admin",
		IsActive: true,
		Username: sql.NullString{String: "admin", Valid: true},
	}
	if err := models.CreateUser(admin); err != nil {
		log.Printf("Warning: admin user might already exist: %v", err)
	}

	// 6. Create Organizer User
	log.Println("Creating organizer user...")
	orgPassword, _ := bcrypt.GenerateFromPassword([]byte("organizer@ingate.id"), bcrypt.DefaultCost)
	organizer := &models.User{
		Name:          "Ingate Promotions",
		Email:         "organizer@ingate.id",
		Password:      string(orgPassword),
		Role:          "organizer",
		IsActive:      true,
		Username:      sql.NullString{String: "ingate_org", Valid: true},
		OrganizerName: sql.NullString{String: "Ingate Promotions", Valid: true},
		AboutUs:       sql.NullString{String: "Leading organizer of international festivals and high-end events.", Valid: true},
		Phone:         sql.NullString{String: "081234567890", Valid: true},
		Province:      sql.NullString{String: "Jawa Timur", Valid: true},
		City:          sql.NullString{String: "Ponorogo", Valid: true},
		ZipCode:       sql.NullString{String: "63411", Valid: true},
		Address:       sql.NullString{String: "Jl. Jendral Sudirman No. 1, Ponorogo", Valid: true},
	}
	if err := models.CreateUser(organizer); err != nil {
		log.Printf("Warning: organizer user might already exist: %v", err)
	}

	// 7. Create Sample Events with Rich Content
	log.Println("Creating sample events...")
	bannerPath := "/uploads/events/1770321313086990300.webp"
	thumbPath := "/uploads/events/1770321313087489500.png"

	events := []struct {
		Name        string
		Slug        string
		Category    string
		City        string
		Description string
	}{
		{
			Name:     "Jakarta International Jazz Festival 2026",
			Slug:     "jakarta-jazz-2026",
			Category: "Music",
			City:     "Jakarta",
			Description: `
				<p>The biggest jazz festival in Southeast Asia is back. Featuring world-class musicians and local legends across 10 different stages.</p>
				<p><strong>Highlights of 2026:</strong></p>
				<ul>
					<li>Exclusive backstage tours for VIP ticket holders.</li>
					<li>Interactive music workshops with industry veterans.</li>
					<li>Gourmet food festival featuring the best of Indonesian cuisine.</li>
				</ul>`,
		},
		{
			Name:     "Tech Expo Asia 2026",
			Slug:     "tech-expo-2026",
			Category: "Technology",
			City:     "Bandung",
			Description: `
				<p>Experience the future of innovation at Tech Expo Asia 2026. Explore the latest in AI, Robotics, and Green Tech.</p>
				<p><strong>What to expect:</strong></p>
				<ul>
					<li>Keynotes from world tech leaders.</li>
					<li>Hands-on workshops.</li>
					<li>Developer hackathons with massive prizes.</li>
				</ul>`,
		},
	}

	for _, eData := range events {
		event := &models.Event{
			Name:          eData.Name,
			Slug:          eData.Slug,
			Category:      eData.Category,
			Status:        "published",
			BannerPath:    &bannerPath,
			ThumbnailPath: &thumbPath,
			StartDate:     time.Now().AddDate(0, 1, 0),
			EndDate:       time.Now().AddDate(0, 1, 1),
			Description:   eData.Description,
			Terms:         stringPtr("<h3>Terms & Conditions</h3><ol><li>Tickets are non-refundable.</li><li>Bring valid ID matching the ticket holder name.</li><li>No sharp objects or prohibited substances allowed.</li></ol>"),
			Location:      stringPtr("Grand Convention Hall"),
			City:          stringPtr(eData.City),
			Province:      stringPtr("Jawa"),
			OrganizerID:   &organizer.ID,
			OrganizerName: stringPtr(organizer.OrganizerName.String),

			// SEO & Media
			SeoTitle:       stringPtr(fmt.Sprintf("%s | Ingate Tickets", eData.Name)),
			SeoDescription: stringPtr(fmt.Sprintf("Buy your tickets for %s at Ingate. Secure, easy, and fast booking.", eData.Name)),
			GoogleMapEmbed: stringPtr("<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.822238495066!2d106.84155157485375!3d-6.154562093832598!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f5926ec09141%3A0xe6798b0492cb2c0d!2sJIEXPO%20Kemayoran!5e0!3m2!1sen!2sid!4v1715830000000!5m2!1sen!2sid\" width=\"100%\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>"),
			YoutubeLink:    stringPtr("https://www.youtube.com/watch?v=FjS6T8Xv2Ww"),

			// Financial Settings
			AdminFee:         5000,
			AdminFeeType:     "fixed",
			PPN:              11,
			PPNType:          "percent",
			OrganizerTax:     5,
			OrganizerTaxType: "percent",
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
			{"VIP Experience", 1500000, 50},
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
				Description:        sql.NullString{String: fmt.Sprintf("Access to %s and all festival areas.", tData.Name), Valid: true},
			}
			if err := models.CreateTicket(ticket); err != nil {
				log.Fatalf("Failed to create ticket %s: %v", ticket.Name, err)
			}
		}
		log.Printf("Created event: %s with tickets", event.Name)
	}

	// 9. Create Banners
	log.Println("Creating sample banners...")
	banners := []struct {
		Slug  string
		Title string
		URL   string
	}{
		{"jazz-fest-2026", "Jakarta Jazz Festival 2026", "/events/jakarta-jazz-2026"},
		{"tech-expo-2026", "Tech Expo Asia 2026", "/events/tech-expo-2026"},
	}

	for _, bData := range banners {
		banner := &models.Banner{
			Slug:      bData.Slug,
			Title:     bData.Title,
			ImagePath: bannerPath,
			LinkURL:   bData.URL,
			IsActive:  true,
		}
		if err := models.CreateBanner(banner); err != nil {
			log.Printf("Warning: failed to create banner %s: %v", bData.Slug, err)
		}
	}

	log.Println("Database reset and seeding successful!")
	log.Println("Admin: admin@ingate.id / admin@ingate.id")
	log.Println("Organizer: organizer@ingate.id / organizer@ingate.id")
}

func stringPtr(s string) *string {
	return &s
}
