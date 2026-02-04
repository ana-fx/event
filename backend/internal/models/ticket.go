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
