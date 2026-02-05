package models

import (
	"database/sql"
	"event-backend/internal/database"
	"time"
)

type Deposit struct {
	ID        int       `json:"id"`
	UserID    int       `json:"user_id"`
	Amount    float64   `json:"amount"`
	Note      string    `json:"note"`
	CreatedAt time.Time `json:"created_at"`
}

func GetResellerBalance(userID int) (float64, error) {
	// Simple calculation: Total Deposits - Total Approved Withdrawals - Total Paid Transactions (where reseller_id = userID)

	var totalDeposits float64
	err := database.DB.QueryRow(`SELECT COALESCE(SUM(amount), 0) FROM reseller_deposits WHERE user_id=$1 AND deleted_at IS NULL`, userID).Scan(&totalDeposits)
	if err != nil {
		return 0, err
	}

	// Withdrawals not yet implemented for resellers in DB schema
	var totalWithdrawals float64 = 0
	// err = database.DB.QueryRow(`SELECT COALESCE(SUM(amount), 0) FROM withdrawals WHERE user_id=$1 AND status='approved' AND deleted_at IS NULL`, userID).Scan(&totalWithdrawals)
	// if err != nil {
	// 	return 0, err
	// }

	// Transactions created by reseller (reseller_id) that are 'paid'
	// Note: If reseller buys ticket, it costs money.
	var totalSpent float64
	// Assuming reseller_id is set when reseller buys.
	err = database.DB.QueryRow(`SELECT COALESCE(SUM(total_price), 0) FROM transactions WHERE reseller_id=$1 AND status='paid' AND deleted_at IS NULL`, userID).Scan(&totalSpent)
	if err != nil {
		return 0, err
	}

	return totalDeposits - totalWithdrawals - totalSpent, nil
}

func GetDeposits(userID int) ([]Deposit, error) {
	rows, err := database.DB.Query(`SELECT id, user_id, amount, note, created_at FROM reseller_deposits WHERE user_id=$1 AND deleted_at IS NULL ORDER BY created_at DESC`, userID)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var deposits []Deposit
	for rows.Next() {
		var d Deposit
		var note sql.NullString
		err := rows.Scan(&d.ID, &d.UserID, &d.Amount, &note, &d.CreatedAt)
		if err != nil {
			return nil, err
		}
		d.Note = note.String
		deposits = append(deposits, d)
	}
	return deposits, nil
}
func CreateDeposit(d *Deposit) error {
	query := `INSERT INTO reseller_deposits (user_id, amount, note, created_at) VALUES ($1, $2, $3, $4) RETURNING id`
	return database.DB.QueryRow(query, d.UserID, d.Amount, d.Note, time.Now()).Scan(&d.ID)
}
