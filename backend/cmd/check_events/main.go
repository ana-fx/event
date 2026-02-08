package main

import (
	"database/sql"
	"fmt"
	"log"
	"os"

	"github.com/joho/godotenv"
	_ "github.com/lib/pq"
)

func main() {
	godotenv.Load("../../.env")

	dbURL := os.Getenv("DATABASE_URL")
	if dbURL == "" {
		dbURL = "postgres://postgres:root@localhost:5432/event_db?sslmode=disable"
	}

	db, err := sql.Open("postgres", dbURL)
	if err != nil {
		log.Fatal(err)
	}
	defer db.Close()

	rows, err := db.Query("SELECT id, name, slug FROM events WHERE deleted_at IS NULL")
	if err != nil {
		log.Fatal(err)
	}
	defer rows.Close()

	fmt.Println("List of Event Slugs in Database:")
	fmt.Println("-------------------------------")
	for rows.Next() {
		var id int
		var name, slug string
		if err := rows.Scan(&id, &name, &slug); err != nil {
			log.Fatal(err)
		}
		fmt.Printf("ID: %d | Name: %s | Slug: %s\n", id, name, slug)
	}
}
