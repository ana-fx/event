package models

import (
	"database/sql"
	"event-backend/internal/database"
	"time"
)

type Transaction struct {
	ID                    int            `json:"id"`
	Code                  string         `json:"code"`
	EventID               int            `json:"event_id"`
	TicketID              int            `json:"ticket_id"`
	Name                  string         `json:"name"`
	Email                 string         `json:"email"`
	Phone                 string         `json:"phone"`
	City                  string         `json:"city"`
	NIK                   string         `json:"nik"`
	Gender                string         `json:"gender"`
	Quantity              int            `json:"quantity"`
	TotalPrice            float64        `json:"total_price"`
	Status                string         `json:"status"`
	RedeemedAt            sql.NullTime   `json:"redeemed_at"`
	RedeemedBy            sql.NullInt64  `json:"redeemed_by"`
	ResellerID            sql.NullInt64  `json:"reseller_id"`
	SnapToken             sql.NullString `json:"snap_token"`
	PaymentType           sql.NullString `json:"payment_type"`
	MidtransTransactionID sql.NullString `json:"midtrans_transaction_id"`
	CreatedAt             time.Time      `json:"created_at"`
	UpdatedAt             time.Time      `json:"updated_at"`
}

func GetAllTransactions(eventID int) ([]Transaction, error) {
	query := `
		SELECT id, code, event_id, ticket_id, name, email, phone, quantity, total_price, status, created_at 
		FROM transactions 
		WHERE deleted_at IS NULL`

	var args []interface{}
	if eventID > 0 {
		query += ` AND event_id = $1`
		args = append(args, eventID)
	}
	query += ` ORDER BY created_at DESC`

	rows, err := database.DB.Query(query, args...)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var transactions []Transaction
	for rows.Next() {
		var t Transaction
		// Scanning subset
		err := rows.Scan(
			&t.ID, &t.Code, &t.EventID, &t.TicketID, &t.Name, &t.Email, &t.Phone,
			&t.Quantity, &t.TotalPrice, &t.Status, &t.CreatedAt,
		)
		if err != nil {
			return nil, err
		}
		transactions = append(transactions, t)
	}
	return transactions, nil
}

func CreateTransaction(t *Transaction) error {
	query := `
		INSERT INTO transactions (
			code, event_id, ticket_id, name, email, phone, city, nik, gender, quantity, total_price, status, created_at, updated_at
		) VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13, $14)
		RETURNING id`

	err := database.DB.QueryRow(query,
		t.Code, t.EventID, t.TicketID, t.Name, t.Email, t.Phone, t.City, t.NIK, t.Gender, t.Quantity, t.TotalPrice, t.Status, time.Now(), time.Now(),
	).Scan(&t.ID)

	return err
}

func SumPaidRevenue() (float64, error) {
	var total sql.NullFloat64
	err := database.DB.QueryRow(`SELECT SUM(total_price) FROM transactions WHERE status = 'paid' AND deleted_at IS NULL`).Scan(&total)
	if err != nil {
		return 0, err
	}
	return total.Float64, nil
}

func UpdateTransactionSnapToken(id int, token string, redirectURL string) error {
	_, err := database.DB.Exec(`UPDATE transactions SET snap_token=$1, updated_at=$2 WHERE id=$3`, token, time.Now(), id)
	return err
}

func GetTransactionByCode(code string) (*Transaction, error) {
	query := `
		SELECT id, code, event_id, ticket_id, name, email, phone, quantity, total_price, status, created_at 
		FROM transactions 
		WHERE code = $1 AND deleted_at IS NULL`
	var t Transaction
	err := database.DB.QueryRow(query, code).Scan(
		&t.ID, &t.Code, &t.EventID, &t.TicketID, &t.Name, &t.Email, &t.Phone,
		&t.Quantity, &t.TotalPrice, &t.Status, &t.CreatedAt,
	)
	if err != nil {
		return nil, err
	}
	return &t, nil
}
