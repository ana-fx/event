package reseller

import (
	"database/sql"
	"encoding/json"
	"event-backend/internal/middleware"
	"event-backend/internal/models"
	"fmt"
	"math/rand"
	"net/http"
	"time"
)

func GetStart(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodGet {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}

	userID := r.Context().Value(middleware.UserIDKey).(int)

	balance, err := models.GetResellerBalance(userID)
	if err != nil {
		http.Error(w, "Failed to get balance", http.StatusInternalServerError)
		return
	}

	deposits, err := models.GetDeposits(userID)
	if err != nil {
		http.Error(w, "Failed to get deposits", http.StatusInternalServerError)
		return
	}

	res := map[string]interface{}{
		"balance":  balance,
		"deposits": deposits,
	}

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(res)
}

func CreateTransaction(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodPost {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}

	userID := r.Context().Value(middleware.UserIDKey).(int)

	var req struct {
		EventID  int    `json:"event_id"`
		TicketID int    `json:"ticket_id"`
		Quantity int    `json:"quantity"`
		Name     string `json:"name"`
		Email    string `json:"email"`
		Phone    string `json:"phone"`
		City     string `json:"city"`
		NIK      string `json:"nik"`
		Gender   string `json:"gender"`
	}
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "Invalid body", http.StatusBadRequest)
		return
	}

	// 1. Check Ticket
	ticket, err := models.GetTicketByID(req.TicketID)
	if err != nil {
		http.Error(w, "Ticket not found", http.StatusNotFound)
		return
	}

	// 2. Check Balance
	totalPrice := ticket.Price * float64(req.Quantity)
	balance, err := models.GetResellerBalance(userID)
	if err != nil {
		http.Error(w, "Failed to check balance", http.StatusInternalServerError)
		return
	}

	if balance < totalPrice {
		http.Error(w, "Insufficient balance", http.StatusBadRequest)
		return
	}

	// 3. Create Transaction (Paid)
	code := fmt.Sprintf("ANNTIX-R-%d-%d", time.Now().Unix(), rand.Intn(1000))

	trx := models.Transaction{
		Code:       code,
		EventID:    ticket.EventID,
		TicketID:   ticket.ID,
		Name:       req.Name,
		Email:      req.Email,
		Phone:      req.Phone,
		City:       req.City,
		NIK:        req.NIK,
		Gender:     req.Gender,
		Quantity:   req.Quantity,
		TotalPrice: totalPrice,
		Status:     "paid", // Reseller pays immediately from wallet
		ResellerID: sql.NullInt64{Int64: int64(userID), Valid: true},
	}

	if err := models.CreateTransaction(&trx); err != nil {
		http.Error(w, "Failed to create transaction: "+err.Error(), http.StatusInternalServerError)
		return
	}

	w.WriteHeader(http.StatusCreated)
	json.NewEncoder(w).Encode(trx)
}
