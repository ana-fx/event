package admin

import (
	"encoding/json"
	"event-backend/internal/models"
	"net/http"
	"strconv"
)

func CreateDeposit(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodPost {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}

	var req models.Deposit
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "Invalid body", http.StatusBadRequest)
		return
	}

	if req.UserID == 0 || req.Amount <= 0 {
		http.Error(w, "User ID and positive Amount required", http.StatusBadRequest)
		return
	}

	if err := models.CreateDeposit(&req); err != nil {
		http.Error(w, "Failed to create deposit: "+err.Error(), http.StatusInternalServerError)
		return
	}

	w.WriteHeader(http.StatusCreated)
	json.NewEncoder(w).Encode(req)
}

func GetUserDeposits(w http.ResponseWriter, r *http.Request) {
	userID, _ := strconv.Atoi(r.URL.Query().Get("user_id"))
	if userID == 0 {
		http.Error(w, "User ID required", http.StatusBadRequest)
		return
	}

	deposits, err := models.GetDeposits(userID)
	if err != nil {
		http.Error(w, "Failed to fetch deposits: "+err.Error(), http.StatusInternalServerError)
		return
	}

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(deposits)
}

func GetResellerBalance(w http.ResponseWriter, r *http.Request) {
	userID, _ := strconv.Atoi(r.URL.Query().Get("user_id"))
	if userID == 0 {
		http.Error(w, "User ID required", http.StatusBadRequest)
		return
	}

	balance, err := models.GetResellerBalance(userID)
	if err != nil {
		http.Error(w, err.Error(), http.StatusInternalServerError)
		return
	}

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]float64{"balance": balance})
}
