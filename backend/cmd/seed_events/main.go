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
)

func main() {
	if err := godotenv.Load(); err != nil {
		log.Println("Warning: .env file not found")
	}

	connStr := os.Getenv("DATABASE_URL")
	if connStr == "" {
		connStr = "postgres://postgres:root@localhost:5432/event_app_db?sslmode=disable"
	}
	database.Connect(connStr)

	log.Println("Seeding multiple events...")

	events := []struct {
		Name     string
		Slug     string
		Category string
		City     string
	}{
		{"Music Festival 2024", "music-fest-2024", "Music", "Jakarta"},
		{"Tech Conference 2024", "tech-conf-2024", "Technology", "Bandung"},
		{"Art Exhibition", "art-exhibition-2024", "Art", "Yogyakarta"},
		{"Food Carnival", "food-carnival-2024", "Food", "Surabaya"},
		{"Startup Summit", "startup-summit-2024", "Business", "Jakarta"},
	}

	for _, eData := range events {
		event := &models.Event{
			Name:          eData.Name,
			Slug:          eData.Slug,
			Category:      eData.Category,
			Status:        "published",
			StartDate:     time.Now().AddDate(0, 1, 0),
			EndDate:       time.Now().AddDate(0, 1, 1),
			Description:   fmt.Sprintf("Welcome to %s! A great event in %s.", eData.Name, eData.City),
			City:          stringPtr(eData.City),
			Location:      stringPtr("Main Venue"),
			OrganizerName: stringPtr("Local Organizer"),
		}

		// Check if exists
		existing, _ := models.GetEventBySlug(event.Slug)
		if existing != nil {
			log.Printf("Event %s already exists, skipping...", event.Slug)
			continue
		}

		err := models.CreateEvent(event)
		if err != nil {
			log.Fatalf("Failed to create event %s: %v", event.Slug, err)
		}

		// Create a sample ticket for each event
		ticket := &models.Ticket{
			EventID:     event.ID,
			Name:        "General Admission",
			Price:       100000,
			Quota:       500,
			IsActive:    true,
			StartDate:   time.Now(),
			EndDate:     event.StartDate,
			Description: sql.NullString{String: "Standard ticket", Valid: true},
		}
		models.CreateTicket(ticket)
		log.Printf("Created event: %s (ID: %d)", event.Name, event.ID)
	}

	log.Println("Bulk seeding completed!")
}

func stringPtr(s string) *string {
	return &s
}
