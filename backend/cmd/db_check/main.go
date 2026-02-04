package main

import (
	"database/sql"
	"fmt"
	"log"

	_ "github.com/lib/pq"
)

func main() {
	// Connect to 'postgres' database first to create the new DB
	connStr := "postgres://postgres:root@localhost:5432/postgres?sslmode=disable"
	db, err := sql.Open("postgres", connStr)
	if err != nil {
		log.Fatal("Error opening connection: ", err)
	}
	defer db.Close()

	// Check connection
	err = db.Ping()
	if err != nil {
		log.Fatalf("Error pinging database: %v\nMake sure PostgreSQL is running on localhost:5432 and password is correct.", err)
	}
	fmt.Println("Successfully connected to PostgreSQL server!")

	// Create database if not exists
	_, err = db.Exec("CREATE DATABASE event_db")
	if err != nil {
		// Ignore error if database already exists (naive check)
		fmt.Printf("Database creation result (might already exist): %v\n", err)
	} else {
		fmt.Println("Database 'event_db' created successfully!")
	}
}
