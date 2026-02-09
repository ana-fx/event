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
	database.RunMigrations()

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
		Description:    "<p>The biggest jazz festival in Southeast Asia is back. Featuring world-class musicians and local legends across 10 different stages.</p><p><strong>Highlights of 2026:</strong></p><ul><li>Exclusive backstage tours for VIP ticket holders.</li><li>Interactive music workshops with industry veterans.</li><li>Gourmet food festival featuring the best of Indonesian cuisine.</li></ul>",
		Location:       stringPtr("JIEXPO Kemayoran"),
		City:           stringPtr("Jakarta"),
		OrganizerName:  stringPtr("Ingate Promotions"),
		SeoTitle:       stringPtr("Jakarta Jazz Festival 2026 | Buy Tickets"),
		SeoDescription: stringPtr("Get your tickets for Jakarta Jazz Festival 2026. Join the best musical experience in the heart of Jakarta."),
		GoogleMapEmbed: stringPtr("<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.822238495066!2d106.84155157485375!3d-6.154562093832598!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f5926ec09141%3A0xe6798b0492cb2c0d!2sJIEXPO%20Kemayoran!5e0!3m2!1sen!2sid!4v1715830000000!5m2!1sen!2sid\" width=\"100%\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>"),
		Terms:          stringPtr("<h3>Syarat & Ketentuan</h3><ol><li>Tiket yang sudah dibeli tidak dapat ditukarkan maupun diuangkan kembali.</li><li>Pengunjung wajib membawa kartu identitas asli (KTP/SIM/Paspor) yang sesuai dengan data pada tiket.</li><li>Panitia berhak melarang pengunjung masuk jika tidak mematuhi protokol kesehatan yang berlaku.</li><li>Anak di bawah usia 12 tahun wajib didampingi oleh orang tua/wali.</li><li>Dilarang membawa senjata tajam, obat-obatan terlarang, dan minuman keras ke dalam area festival.</li></ol>"),
		YoutubeLink:    stringPtr("https://www.youtube.com/watch?v=FjS6T8Xv2Ww"),
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
