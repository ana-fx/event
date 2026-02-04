package models

import (
	"database/sql"
	"event-backend/internal/database"
	"time"
)

type Withdrawal struct {
	ID        int            `json:"id"`
	EventID   int            `json:"event_id"`
	Amount    float64        `json:"amount"`
	Reference string         `json:"reference"`
	Note      sql.NullString `json:"note"`
	CreatedAt time.Time      `json:"created_at"`
	UpdatedAt time.Time      `json:"updated_at"`
}

func GetEventWithdrawals(eventID int) ([]Withdrawal, error) {
	rows, err := database.DB.Query(`SELECT id, event_id, amount, reference, note, created_at FROM withdrawals WHERE event_id=$1 ORDER BY created_at DESC`, eventID)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var withdrawals []Withdrawal
	for rows.Next() {
		var w Withdrawal
		err := rows.Scan(&w.ID, &w.EventID, &w.Amount, &w.Reference, &w.Note, &w.CreatedAt)
		if err != nil {
			return nil, err
		}
		withdrawals = append(withdrawals, w)
	}
	return withdrawals, nil
}

func CreateWithdrawal(w *Withdrawal) error {
	query := `INSERT INTO withdrawals (event_id, amount, reference, note, created_at, updated_at) VALUES ($1, $2, $3, $4, $5, $6) RETURNING id`
	return database.DB.QueryRow(query, w.EventID, w.Amount, w.Reference, w.Note, time.Now(), time.Now()).Scan(&w.ID)
}

func GetTotalWithdrawals(eventID int) (float64, error) {
	var total sql.NullFloat64
	err := database.DB.QueryRow(`SELECT SUM(amount) FROM withdrawals WHERE event_id=$1`, eventID).Scan(&total)
	if err != nil {
		return 0, err
	}
	return total.Float64, nil
}
