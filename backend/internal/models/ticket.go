package models

import (
	"database/sql"
	"event-backend/internal/database"
	"time"
)

type Ticket struct {
	ID                 int            `json:"id"`
	EventID            int            `json:"event_id"`
	Name               string         `json:"name"`
	Description        sql.NullString `json:"description"`
	IsActive           bool           `json:"is_active"`
	Price              float64        `json:"price"`
	Quota              int            `json:"quota"`
	MaxPurchasePerUser int            `json:"max_purchase_per_user"`
	StartDate          time.Time      `json:"start_date"`
	EndDate            time.Time      `json:"end_date"`
	CreatedAt          time.Time      `json:"created_at"`
	UpdatedAt          time.Time      `json:"updated_at"`
}

func GetTicketByID(id int) (*Ticket, error) {
	query := `SELECT id, event_id, name, price, quota, max_purchase_per_user, start_date, end_date FROM tickets WHERE id=$1`
	var t Ticket
	err := database.DB.QueryRow(query, id).Scan(
		&t.ID, &t.EventID, &t.Name, &t.Price, &t.Quota, &t.MaxPurchasePerUser, &t.StartDate, &t.EndDate,
	)
	if err != nil {
		return nil, err
	}
	return &t, nil
}

func CreateTicket(t *Ticket) error {
	query := `
		INSERT INTO tickets (event_id, name, description, is_active, price, quota, max_purchase_per_user, start_date, end_date, created_at, updated_at) 
		VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11) 
		RETURNING id`
	return database.DB.QueryRow(query,
		t.EventID, t.Name,
		sql.NullString{String: t.Description.String, Valid: t.Description.String != ""},
		t.IsActive, t.Price, t.Quota, t.MaxPurchasePerUser, t.StartDate, t.EndDate,
		time.Now(), time.Now(),
	).Scan(&t.ID)
}

func UpdateTicket(t *Ticket) error {
	query := `
		UPDATE tickets 
		SET name=$1, description=$2, is_active=$3, price=$4, quota=$5, max_purchase_per_user=$6, start_date=$7, end_date=$8, updated_at=$9
		WHERE id=$10`
	_, err := database.DB.Exec(query,
		t.Name,
		sql.NullString{String: t.Description.String, Valid: t.Description.String != ""},
		t.IsActive, t.Price, t.Quota, t.MaxPurchasePerUser, t.StartDate, t.EndDate,
		time.Now(), t.ID,
	)
	return err
}

func DeleteTicket(id int) error {
	query := `UPDATE tickets SET deleted_at=$1 WHERE id=$2`
	_, err := database.DB.Exec(query, time.Now(), id)
	return err
}

func GetTicketsByEventID(eventID int) ([]Ticket, error) {
	rows, err := database.DB.Query(`
		SELECT id, event_id, name, description, price, quota, max_purchase_per_user, start_date, end_date, is_active 
		FROM tickets 
		WHERE event_id=$1 AND deleted_at IS NULL
		ORDER BY price ASC`, eventID)
	if err != nil {
		return nil, err
	}
	defer rows.Close()
	var tickets []Ticket
	for rows.Next() {
		var t Ticket
		if err := rows.Scan(
			&t.ID, &t.EventID, &t.Name, &t.Description, &t.Price, &t.Quota, &t.MaxPurchasePerUser, &t.StartDate, &t.EndDate, &t.IsActive,
		); err != nil {
			return nil, err
		}
		tickets = append(tickets, t)
	}
	return tickets, nil
}
