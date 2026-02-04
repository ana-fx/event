package public

import (
	"encoding/json"
	"event-backend/internal/database"
	"event-backend/internal/models"
	"net/http"
)

func GetTransactionStatus(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodGet {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}
	code := r.URL.Query().Get("code")
	if code == "" {
		http.Error(w, "Code required", http.StatusBadRequest)
		return
	}

	// Fetch transaction by code
	var t models.Transaction
	query := `SELECT id, code, event_id, name, email, quantity, total_price, status, created_at FROM transactions WHERE code=$1`
	err := database.DB.QueryRow(query, code).Scan(&t.ID, &t.Code, &t.EventID, &t.Name, &t.Email, &t.Quantity, &t.TotalPrice, &t.Status, &t.CreatedAt)
	if err != nil {
		http.Error(w, "Transaction not found", http.StatusNotFound)
		return
	}

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(t)
}
