package database

import (
	"embed"
	"log"

	"github.com/pressly/goose/v3"
)

//go:embed *.sql
var embedMigrations embed.FS

func RunMigrations() {
	goose.SetBaseFS(embedMigrations)

	if err := goose.SetDialect("postgres"); err != nil {
		log.Fatal("Failed to set goose dialect:", err)
	}

	log.Println("Running database migrations...")
	if err := goose.Up(DB, "migrations"); err != nil {
		log.Fatal("Failed to run migrations:", err)
	}
	log.Println("Migrations executed successfully!")
}
