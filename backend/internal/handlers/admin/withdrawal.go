package admin

import (
	"database/sql"
	"encoding/json"
	"event-backend/internal/models"
	"net/http"
	"strconv"
)

func ListWithdrawals(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodGet {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}
	eventIDStr := r.URL.Query().Get("event_id")
	eventID, err := strconv.Atoi(eventIDStr)
	if err != nil {
		http.Error(w, "Invalid event_id", http.StatusBadRequest)
		return
	}

	withdrawals, err := models.GetEventWithdrawals(eventID)
	if err != nil {
		http.Error(w, err.Error(), http.StatusInternalServerError)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(withdrawals)
}

func CreateWithdrawal(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodPost {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}

	var req struct {
		EventID   int     `json:"event_id"`
		Amount    float64 `json:"amount"`
		Reference string  `json:"reference"`
		Note      string  `json:"note"`
	}
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "Invalid body", http.StatusBadRequest)
		return
	}

	wd := models.Withdrawal{
		EventID:   req.EventID,
		Amount:    req.Amount,
		Reference: req.Reference,
		Note:      sql.NullString{String: req.Note, Valid: req.Note != ""},
	}

	if err := models.CreateWithdrawal(&wd); err != nil {
		http.Error(w, "Failed to create: "+err.Error(), http.StatusInternalServerError)
		return
	}

	w.WriteHeader(http.StatusCreated)
	json.NewEncoder(w).Encode(wd)
}
