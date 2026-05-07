package scanner

import (
	"database/sql"
	"encoding/json"
	"event-backend/internal/database"
	"event-backend/internal/models"
	"net/http"
	"time"
)

func isAssignedToEvent(scannerID, eventID int) bool {
	var exists bool
	err := database.DB.QueryRow(
		`SELECT EXISTS(SELECT 1 FROM event_scanner WHERE user_id=$1 AND event_id=$2)`,
		scannerID, eventID,
	).Scan(&exists)
	return err == nil && exists
}

func Verify(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodPost {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}

	scannerID, ok := r.Context().Value(models.UserIDKey).(int)
	if !ok {
		http.Error(w, "Unauthorized", http.StatusUnauthorized)
		return
	}

	var req struct {
		Code    string `json:"code"`
		EventID int    `json:"event_id"`
	}
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "Invalid body", http.StatusBadRequest)
		return
	}

	if req.EventID == 0 {
		http.Error(w, "event_id is required", http.StatusBadRequest)
		return
	}

	if !isAssignedToEvent(scannerID, req.EventID) {
		http.Error(w, "Forbidden: You are not assigned to this event", http.StatusForbidden)
		return
	}

	query := `
		SELECT id, name, email, quantity, status, redeemed_at, event_id
		FROM transactions
		WHERE code = $1 LIMIT 1`

	var t struct {
		ID         int
		Name       string
		Email      string
		Quantity   sql.NullInt64
		Status     string
		RedeemedAt sql.NullTime
		EventID    int
	}

	err := database.DB.QueryRow(query, req.Code).Scan(&t.ID, &t.Name, &t.Email, &t.Quantity, &t.Status, &t.RedeemedAt, &t.EventID)
	if err != nil {
		if err == sql.ErrNoRows {
			http.Error(w, "Ticket not found", http.StatusNotFound)
			return
		}
		http.Error(w, "Database error: "+err.Error(), http.StatusInternalServerError)
		return
	}

	if t.EventID != req.EventID {
		w.Header().Set("Content-Type", "application/json")
		json.NewEncoder(w).Encode(map[string]interface{}{
			"valid": false,
			"msg":   "Ticket does not belong to this event",
		})
		return
	}

	var items []models.TransactionItem
	rows, err := database.DB.Query(`SELECT id, ticket_id, name, quantity FROM transaction_items WHERE transaction_id = $1`, t.ID)
	if err == nil {
		defer rows.Close()
		for rows.Next() {
			var item models.TransactionItem
			if err := rows.Scan(&item.ID, &item.TicketID, &item.Name, &item.Quantity); err == nil {
				items = append(items, item)
			}
		}
	}

	totalQty := 0
	if t.Quantity.Valid {
		totalQty = int(t.Quantity.Int64)
	} else {
		for _, item := range items {
			totalQty += item.Quantity
		}
	}

	res := map[string]interface{}{
		"valid":    false,
		"data":     t,
		"quantity": totalQty,
		"items":    items,
		"msg":      "Invalid",
	}

	if t.Status == "paid" {
		if t.RedeemedAt.Valid {
			res["msg"] = "Already Redeemed"
		} else {
			res["valid"] = true
			res["msg"] = "Valid Ticket"
		}
	} else {
		res["msg"] = "Ticket status: " + t.Status
	}

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(res)
}

func Redeem(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodPost {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}

	scannerID, ok := r.Context().Value(models.UserIDKey).(int)
	if !ok {
		http.Error(w, "Unauthorized", http.StatusUnauthorized)
		return
	}

	var req struct {
		Code    string `json:"code"`
		EventID int    `json:"event_id"`
	}
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "Invalid body", http.StatusBadRequest)
		return
	}

	if req.EventID == 0 {
		http.Error(w, "event_id is required", http.StatusBadRequest)
		return
	}

	if !isAssignedToEvent(scannerID, req.EventID) {
		http.Error(w, "Forbidden: You are not assigned to this event", http.StatusForbidden)
		return
	}

	query := `
		UPDATE transactions
		SET redeemed_at = $1, redeemed_by = $2
		WHERE code = $3 AND event_id = $4 AND status = 'paid' AND redeemed_at IS NULL
		RETURNING id`

	var id int
	err := database.DB.QueryRow(query, time.Now(), scannerID, req.Code, req.EventID).Scan(&id)
	if err != nil {
		if err == sql.ErrNoRows {
			http.Error(w, "Cannot redeem: Ticket invalid, not paid, already used, or wrong event", http.StatusBadRequest)
			return
		}
		http.Error(w, "Database error: "+err.Error(), http.StatusInternalServerError)
		return
	}

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]string{"status": "success", "msg": "Ticket Redeemed"})
}
