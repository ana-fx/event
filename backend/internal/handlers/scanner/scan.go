package scanner

import (
	"database/sql"
	"encoding/json"
	"event-backend/internal/database"
	"event-backend/internal/middleware"
	"net/http"
	"time"
)

func Verify(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodPost {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}

	var req struct {
		Code string `json:"code"`
	}
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "Invalid body", http.StatusBadRequest)
		return
	}

	// Fetch Transaction
	query := `
		SELECT id, name, email, quantity, status, redeemed_at 
		FROM transactions 
		WHERE code = $1 LIMIT 1`

	var t struct {
		ID         int
		Name       string
		Email      string
		Quantity   int
		Status     string
		RedeemedAt sql.NullTime
	}

	err := database.DB.QueryRow(query, req.Code).Scan(&t.ID, &t.Name, &t.Email, &t.Quantity, &t.Status, &t.RedeemedAt)
	if err != nil {
		if err == sql.ErrNoRows {
			http.Error(w, "Ticket not found", http.StatusNotFound)
			return
		}
		http.Error(w, "Database error", http.StatusInternalServerError)
		return
	}

	res := map[string]interface{}{
		"valid": false,
		"data":  t,
		"msg":   "Invalid",
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

	// Validate Auth User (Scanner)
	scannerID, ok := r.Context().Value(middleware.UserIDKey).(int)
	if !ok {
		http.Error(w, "Unauthorized", http.StatusUnauthorized)
		return
	}

	var req struct {
		Code string `json:"code"`
	}
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "Invalid body", http.StatusBadRequest)
		return
	}

	// Check if already redeemed
	// Optimistic update
	query := `
		UPDATE transactions 
		SET redeemed_at = $1, redeemed_by = $2 
		WHERE code = $3 AND status = 'paid' AND redeemed_at IS NULL
		RETURNING id`

	var id int
	err := database.DB.QueryRow(query, time.Now(), scannerID, req.Code).Scan(&id)

	if err != nil {
		if err == sql.ErrNoRows {
			// Either not found, not paid, or already redeemed
			http.Error(w, "Cannot redeem: Ticket invalid, not paid, or already used", http.StatusBadRequest)
			return
		}
		http.Error(w, "Database error: "+err.Error(), http.StatusInternalServerError)
		return
	}

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]string{"status": "success", "msg": "Ticket Redeemed"})
}
