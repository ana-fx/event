package main

import (
	"database/sql"
	"event-backend/internal/database"
	"event-backend/internal/models"
	"fmt"
	"log"
	"math/rand"
	"os"
	"time"

	"github.com/joho/godotenv"
)

func main() {
	// 1. Load .env
	if err := godotenv.Load(".env"); err != nil {
		godotenv.Load("../.env")
	}

	// 2. Connect to Database
	connStr := os.Getenv("DATABASE_URL")
	if connStr == "" {
		connStr = "postgres://postgres:root@localhost:5432/event_app_db?sslmode=disable"
	}
	database.Connect(connStr)

	log.Println("Seeding dummy transactions...")

	// 3. Get all published events
	events, err := models.GetPublishedEvents()
	if err != nil {
		log.Fatalf("Failed to fetch events: %v", err)
	}

	if len(events) == 0 {
		log.Fatal("No published events found. Please seed events first.")
	}

	rand.Seed(time.Now().UnixNano())

	names := []string{"Budi Santoso", "Siti Aminah", "Andi Wijaya", "Dewi Lestari", "Rizky Pratama", "Larasati", "Joko Susilo", "Maya Putri"}
	cities := []string{"Jakarta", "Bandung", "Surabaya", "Medan", "Semarang", "Yogyakarta"}
	statuses := []string{"paid", "pending", "cancelled"}

	for _, event := range events {
		tickets, err := models.GetTicketsByEventID(event.ID)
		if err != nil {
			log.Printf("Failed to fetch tickets for event %s: %v", event.Name, err)
			continue
		}

		if len(tickets) == 0 {
			log.Printf("No tickets found for event %s, skipping...", event.Name)
			continue
		}

		// Create 5 transactions per event
		for i := 0; i < 5; i++ {
			ticket := tickets[rand.Intn(len(tickets))]
			qty := rand.Intn(3) + 1
			status := statuses[rand.Intn(len(statuses))]
			
			// Simple calculation for dummy data (no fees included for simplicity in seeder)
			totalPrice := ticket.Price * float64(qty)
			
			code := fmt.Sprintf("DUMMY-%d-%d", time.Now().Unix(), rand.Intn(10000))
			name := names[rand.Intn(len(names))]
			
			trx := &models.Transaction{
				Code:        code,
				EventID:     event.ID,
				TicketID:    sql.NullInt64{Int64: int64(ticket.ID), Valid: true},
				Name:        name,
				Email:       fmt.Sprintf("%s%d@example.com", name, i),
				Phone:       fmt.Sprintf("0812%d", rand.Intn(100000000)),
				City:        cities[rand.Intn(len(cities))],
				NIK:         fmt.Sprintf("3201%d", rand.Intn(100000000)),
				Gender:      []string{"male", "female"}[rand.Intn(2)],
				Quantity:    sql.NullInt64{Int64: int64(qty), Valid: true},
				TotalPrice:  totalPrice,
				Status:      status,
				PaymentType: sql.NullString{String: "bank_transfer", Valid: true},
				Items: []models.TransactionItem{
					{
						TicketID: ticket.ID,
						Name:     ticket.Name,
						Price:    ticket.Price,
						Quantity: qty,
					},
				},
			}

			if err := models.CreateTransaction(trx); err != nil {
				log.Printf("Failed to create transaction %s: %v", code, err)
			} else {
				log.Printf("Created dummy transaction: %s (Status: %s, Event: %s)", code, status, event.Name)
			}
		}
	}

	log.Println("Dummy transaction seeding completed!")
}
