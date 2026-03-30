package models

import (
	"database/sql"
	"event-backend/internal/database"
	"fmt"
	"time"

	"github.com/lib/pq"
)

type Transaction struct {
	ID                    int               `json:"id"`
	Code                  string            `json:"code"`
	EventID               int               `json:"event_id"`
	TicketID              sql.NullInt64     `json:"ticket_id"`
	Name                  string            `json:"name"`
	Email                 string            `json:"email"`
	Phone                 string            `json:"phone"`
	City                  string            `json:"city"`
	NIK                   string            `json:"nik"`
	Gender                string            `json:"gender"`
	Quantity              sql.NullInt64     `json:"quantity"`
	TotalPrice            float64           `json:"total_price"`
	Status                string            `json:"status"`
	EventName             string            `json:"event_name,omitempty"`
	TicketName            string            `json:"ticket_name,omitempty"`
	RedeemedAt            sql.NullTime      `json:"redeemed_at"`
	RedeemedBy            sql.NullInt64     `json:"redeemed_by"`
	ResellerID            sql.NullInt64     `json:"reseller_id"`
	SnapToken             sql.NullString    `json:"snap_token"`
	RedirectURL           sql.NullString    `json:"redirect_url"`
	PaymentType           sql.NullString    `json:"payment_type"`
	MidtransTransactionID sql.NullString    `json:"midtrans_transaction_id"`
	CreatedAt             time.Time         `json:"created_at"`
	UpdatedAt             time.Time         `json:"updated_at"`
	Items                 []TransactionItem `json:"items,omitempty"`
}

type TransactionItem struct {
	ID            int       `json:"id"`
	TransactionID int       `json:"transaction_id"`
	TicketID      int       `json:"ticket_id"`
	Name          string    `json:"name"`
	Price         float64   `json:"price"`
	Quantity      int       `json:"quantity"`
	CreatedAt     time.Time `json:"created_at"`
	UpdatedAt     time.Time `json:"updated_at"`
}

func GetAllTransactions(eventID int, organizerID *int) ([]Transaction, error) {
	query := `
		SELECT t.id, t.code, t.event_id, t.ticket_id, t.name, t.email, t.phone, t.quantity, t.total_price, t.status, t.created_at,
		       COALESCE(e.name, '') as event_name, COALESCE(tk.name, '') as ticket_name
		FROM transactions t
		LEFT JOIN events e ON t.event_id = e.id
		LEFT JOIN tickets tk ON t.ticket_id = tk.id
		WHERE t.deleted_at IS NULL`

	var args []interface{}
	argCount := 1
	if eventID > 0 {
		query += fmt.Sprintf(" AND t.event_id = $%d", argCount)
		args = append(args, eventID)
		argCount++
	}
	if organizerID != nil {
		query += fmt.Sprintf(" AND e.organizer_id = $%d", argCount)
		args = append(args, *organizerID)
		argCount++
	}
	query += ` ORDER BY t.created_at DESC`

	rows, err := database.DB.Query(query, args...)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var transactions []Transaction
	var transactionIDs []int

	for rows.Next() {
		var t Transaction
		err := rows.Scan(
			&t.ID, &t.Code, &t.EventID, &t.TicketID, &t.Name, &t.Email, &t.Phone,
			&t.Quantity, &t.TotalPrice, &t.Status, &t.CreatedAt,
			&t.EventName, &t.TicketName,
		)
		if err != nil {
			return nil, err
		}
		transactions = append(transactions, t)
		transactionIDs = append(transactionIDs, t.ID)
	}

	// Batch load Items
	if len(transactions) > 0 {
		// Prepare query for items
		// PostgreSQL ANY needs array
		itemsQuery := `
			SELECT id, transaction_id, ticket_id, name, price, quantity, created_at 
			FROM transaction_items 
			WHERE transaction_id = ANY($1)`

		itemRows, err := database.DB.Query(itemsQuery, pq.Array(transactionIDs))
		if err != nil {
			// Log error but don't fail the whole report? Or fail?
			// For now, let's return error
			return nil, fmt.Errorf("failed to load items: %v", err)
		}
		defer itemRows.Close()

		// Map items to transactions
		itemsMap := make(map[int][]TransactionItem)
		for itemRows.Next() {
			var ti TransactionItem
			if err := itemRows.Scan(&ti.ID, &ti.TransactionID, &ti.TicketID, &ti.Name, &ti.Price, &ti.Quantity, &ti.CreatedAt); err == nil {
				itemsMap[ti.TransactionID] = append(itemsMap[ti.TransactionID], ti)
			}
		}

		// Attach items
		for i := range transactions {
			if items, ok := itemsMap[transactions[i].ID]; ok {
				transactions[i].Items = items
			}
		}
	}

	return transactions, nil
}

func CreateTransaction(t *Transaction) error {
	query := `
		INSERT INTO transactions (
			code, event_id, ticket_id, name, email, phone, city, nik, gender, quantity, total_price, status, payment_type, created_at, updated_at
		) VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13, $14, $15)
		RETURNING id`

	err := database.DB.QueryRow(query,
		t.Code, t.EventID, t.TicketID, t.Name, t.Email, t.Phone, t.City, t.NIK, t.Gender, t.Quantity, t.TotalPrice, t.Status, t.PaymentType, time.Now(), time.Now(),
	).Scan(&t.ID)
	if err != nil {
		return err
	}

	// Insert Items if any
	for i := range t.Items {
		t.Items[i].TransactionID = t.ID
		_, err := database.DB.Exec(`
			INSERT INTO transaction_items (transaction_id, ticket_id, name, price, quantity, created_at, updated_at)
			VALUES ($1, $2, $3, $4, $5, $6, $7)`,
			t.ID, t.Items[i].TicketID, t.Items[i].Name, t.Items[i].Price, t.Items[i].Quantity, time.Now(), time.Now(),
		)
		if err != nil {
			return fmt.Errorf("failed to insert transaction item: %v", err)
		}
	}

	return nil
}

func UpdateTransactionSnapToken(id int, token string, redirectURL string) error {
	_, err := database.DB.Exec(`UPDATE transactions SET snap_token=$1, redirect_url=$2, updated_at=$3 WHERE id=$4`, token, redirectURL, time.Now(), id)
	return err
}

func GetTransactionByCode(code string) (*Transaction, error) {
	query := `
		SELECT t.id, t.code, t.event_id, t.ticket_id, t.name, t.email, t.phone, t.city, t.nik, t.gender, t.quantity, t.total_price, t.status, t.created_at,
		       t.snap_token, t.redirect_url, t.payment_type, t.midtrans_transaction_id,
		       COALESCE(e.name, '') as event_name, COALESCE(tk.name, '') as ticket_name
		FROM transactions t
		LEFT JOIN events e ON t.event_id = e.id
		LEFT JOIN tickets tk ON t.ticket_id = tk.id
		WHERE t.code = $1 AND t.deleted_at IS NULL`
	var t Transaction
	err := database.DB.QueryRow(query, code).Scan(
		&t.ID, &t.Code, &t.EventID, &t.TicketID, &t.Name, &t.Email, &t.Phone, &t.City, &t.NIK, &t.Gender,
		&t.Quantity, &t.TotalPrice, &t.Status, &t.CreatedAt, &t.SnapToken, &t.RedirectURL, &t.PaymentType, &t.MidtransTransactionID,
		&t.EventName, &t.TicketName,
	)
	if err != nil {
		return nil, err
	}

	// Load Items
	rows, err := database.DB.Query(`SELECT id, transaction_id, ticket_id, name, price, quantity, created_at FROM transaction_items WHERE transaction_id = $1`, t.ID)
	if err == nil {
		defer rows.Close()
		for rows.Next() {
			var item TransactionItem
			if err := rows.Scan(&item.ID, &item.TransactionID, &item.TicketID, &item.Name, &item.Price, &item.Quantity, &item.CreatedAt); err == nil {
				t.Items = append(t.Items, item)
			}
		}
	}

	return &t, nil
}
