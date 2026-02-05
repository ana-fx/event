package admin

import (
	"database/sql"
	"encoding/json"
	"event-backend/internal/models"
	"net/http"
	"strconv"
	"time"
)

// Request struct for parsing JSON body
type TicketRequest struct {
	EventID            int     `json:"event_id"`
	Name               string  `json:"name"`
	Description        string  `json:"description"`
	Price              float64 `json:"price"`
	Quota              int     `json:"quota"`
	MaxPurchasePerUser int     `json:"max_purchase_per_user"`
	StartDate          string  `json:"start_date"`
	EndDate            string  `json:"end_date"`
	IsActive           bool    `json:"is_active"`
}

func CreateTicket(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodPost {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}

	var req TicketRequest
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "Invalid body", http.StatusBadRequest)
		return
	}

	layout := "2006-01-02T15:04" // ISO 8601 partial (without seconds/timezone) - ideally use time.RFC3339 if frontend sends full ISO
	// Try parsing standard ISO if above fails
	start, err := time.Parse(layout, req.StartDate)
	if err != nil {
		start, _ = time.Parse(time.RFC3339, req.StartDate)
	}
	end, err := time.Parse(layout, req.EndDate)
	if err != nil {
		end, _ = time.Parse(time.RFC3339, req.EndDate)
	}

	t := models.Ticket{
		EventID:            req.EventID,
		Name:               req.Name,
		Description:        sql.NullString{String: req.Description, Valid: req.Description != ""},
		Price:              req.Price,
		Quota:              req.Quota,
		MaxPurchasePerUser: req.MaxPurchasePerUser,
		StartDate:          start,
		EndDate:            end,
		IsActive:           req.IsActive,
	}

	if err := models.CreateTicket(&t); err != nil {
		http.Error(w, "Failed to create ticket: "+err.Error(), http.StatusInternalServerError)
		return
	}

	w.WriteHeader(http.StatusCreated)
	json.NewEncoder(w).Encode(t)
}

func UpdateTicket(w http.ResponseWriter, r *http.Request) {
	id, _ := strconv.Atoi(r.URL.Query().Get("id"))
	if id == 0 {
		http.Error(w, "Missing ticket ID", http.StatusBadRequest)
		return
	}

	var req TicketRequest
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "Invalid body", http.StatusBadRequest)
		return
	}

	layout := "2006-01-02T15:04"
	start, err := time.Parse(layout, req.StartDate)
	if err != nil {
		start, _ = time.Parse(time.RFC3339, req.StartDate)
	}
	end, err := time.Parse(layout, req.EndDate)
	if err != nil {
		end, _ = time.Parse(time.RFC3339, req.EndDate)
	}

	t := models.Ticket{
		ID:                 id,
		EventID:            req.EventID,
		Name:               req.Name,
		Description:        sql.NullString{String: req.Description, Valid: req.Description != ""},
		Price:              req.Price,
		Quota:              req.Quota,
		MaxPurchasePerUser: req.MaxPurchasePerUser,
		StartDate:          start,
		EndDate:            end,
		IsActive:           req.IsActive,
	}

	if err := models.UpdateTicket(&t); err != nil {
		http.Error(w, "Failed to update ticket: "+err.Error(), http.StatusInternalServerError)
		return
	}

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(t)
}

func DeleteTicket(w http.ResponseWriter, r *http.Request) {
	id, _ := strconv.Atoi(r.URL.Query().Get("id"))
	if id == 0 {
		http.Error(w, "Missing ticket ID", http.StatusBadRequest)
		return
	}

	if err := models.DeleteTicket(id); err != nil {
		http.Error(w, "Failed to delete ticket", http.StatusInternalServerError)
		return
	}
	w.WriteHeader(http.StatusOK)
}

func ListTickets(w http.ResponseWriter, r *http.Request) {
	eventID, _ := strconv.Atoi(r.URL.Query().Get("event_id"))
	tickets, err := models.GetTicketsByEventID(eventID)
	if err != nil {
		http.Error(w, err.Error(), http.StatusInternalServerError)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(tickets)
}
