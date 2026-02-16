package admin

import (
	"encoding/json"
	"event-backend/internal/models"
	"net/http"
	"strconv"
)

func TransactionReport(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodGet {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}

	eventIDStr := r.URL.Query().Get("event_id")
	eventID := 0
	if eventIDStr != "" {
		var err error
		eventID, err = strconv.Atoi(eventIDStr)
		if err != nil {
			http.Error(w, "Invalid event_id", http.StatusBadRequest)
			return
		}
	}

	transactions, err := models.GetAllTransactions(eventID)
	if err != nil {
		http.Error(w, "Failed to fetch report: "+err.Error(), http.StatusInternalServerError)
		return
	}

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(transactions)
}

func GetTransactionDetail(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodGet {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}

	code := r.URL.Query().Get("code")
	if code == "" {
		http.Error(w, "Transaction code is required", http.StatusBadRequest)
		return
	}

	transaction, err := models.GetTransactionByCode(code)
	if err != nil {
		http.Error(w, "Transaction not found: "+err.Error(), http.StatusNotFound)
		return
	}

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(transaction)
}
