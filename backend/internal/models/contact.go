package models

import (
	"event-backend/internal/database"
	"time"
)

type Contact struct {
	ID        int       `json:"id"`
	Name      string    `json:"name"`
	Email     string    `json:"email"`
	Subject   string    `json:"subject"`
	Message   string    `json:"message"`
	CreatedAt time.Time `json:"created_at"`
	UpdatedAt time.Time `json:"updated_at"`
}

func CreateContact(c *Contact) error {
	query := `INSERT INTO contacts (name, email, subject, message, created_at, updated_at) VALUES ($1, $2, $3, $4, $5, $6) RETURNING id`
	return database.DB.QueryRow(query, c.Name, c.Email, c.Subject, c.Message, time.Now(), time.Now()).Scan(&c.ID)
}

func GetAllContacts() ([]Contact, error) {
	rows, err := database.DB.Query(`SELECT id, name, email, subject, message, created_at FROM contacts ORDER BY created_at DESC`)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var contacts []Contact
	for rows.Next() {
		var c Contact
		err := rows.Scan(&c.ID, &c.Name, &c.Email, &c.Subject, &c.Message, &c.CreatedAt)
		if err != nil {
			return nil, err
		}
		contacts = append(contacts, c)
	}
	return contacts, nil
}

func DeleteContact(id int) error {
	_, err := database.DB.Exec(`DELETE FROM contacts WHERE id=$1`, id)
	return err
}
