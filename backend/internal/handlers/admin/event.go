package admin

import (
	"encoding/json"
	"event-backend/internal/models"
	"net/http"
	"strconv"
	"time"
)

func ListEvents(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodGet {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}

	events, err := models.GetAllEvents()
	if err != nil {
		http.Error(w, "Failed to fetch events: "+err.Error(), http.StatusInternalServerError)
		return
	}

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(events)
}

func CreateEvent(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodPost {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}

	var req models.Event
	// Set defaults or required fields handling
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "Invalid request body", http.StatusBadRequest)
		return
	}

	// Basic validation
	if req.Name == "" || req.Slug == "" {
		http.Error(w, "Name and Slug are required", http.StatusBadRequest)
		return
	}
	// Use explicit dates or defaults
	if req.StartDate.IsZero() {
		req.StartDate = time.Now()
	}
	if req.EndDate.IsZero() {
		req.EndDate = time.Now().Add(24 * time.Hour)
	}

	if err := models.CreateEvent(&req); err != nil {
		http.Error(w, "Failed to create event: "+err.Error(), http.StatusInternalServerError)
		return
	}

	w.Header().Set("Content-Type", "application/json")
	w.WriteHeader(http.StatusCreated)
	json.NewEncoder(w).Encode(req)
}

func UpdateEvent(w http.ResponseWriter, r *http.Request) {
	// Logic to parse ID from URL in standard Go net/http requires parsing r.URL.Path
	// Since we wired it as /api/admin/events (create/list), we might need a separate route for /api/admin/events/update?id=...
	// OR use a proper router.
	// For now, let's assume ID is passed in Query for Update/Delete to keep main.go simple until we refactor routing.

	if r.Method != http.MethodPut && r.Method != http.MethodPost { // Laravel often uses POST with _method=PUT, but API should be PUT
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}

	idStr := r.URL.Query().Get("id")
	if idStr == "" {
		http.Error(w, "ID required", http.StatusBadRequest)
		return
	}

	id, err := strconv.Atoi(idStr)
	if err != nil {
		http.Error(w, "Invalid ID format", http.StatusBadRequest)
		return
	}

	var req models.Event
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "Invalid body", http.StatusBadRequest)
		return
	}
	req.ID = id
	req.UpdatedAt = time.Now()

	if err := models.UpdateEvent(&req); err != nil {
		http.Error(w, "Failed to update: "+err.Error(), http.StatusInternalServerError)
		return
	}

	w.WriteHeader(http.StatusOK)
	json.NewEncoder(w).Encode(map[string]interface{}{"status": "updated", "event": req})
}

func DeleteEvent(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodDelete {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}

	idStr := r.URL.Query().Get("id")
	if idStr == "" {
		http.Error(w, "ID required", http.StatusBadRequest)
		return
	}

	id, err := strconv.Atoi(idStr)
	if err != nil {
		http.Error(w, "Invalid ID format", http.StatusBadRequest)
		return
	}

	if err := models.DeleteEvent(id); err != nil {
		http.Error(w, "Failed to delete: "+err.Error(), http.StatusInternalServerError)
		return
	}

	w.WriteHeader(http.StatusOK)
	json.NewEncoder(w).Encode(map[string]string{"status": "deleted"})
}

// Assignments

func AssignScanner(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodPost {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}
	var req struct {
		EventID int `json:"event_id"`
		UserID  int `json:"user_id"`
	}
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "Invalid body", http.StatusBadRequest)
		return
	}
	if err := models.AssignScanner(req.EventID, req.UserID); err != nil {
		http.Error(w, "Failed to assign: "+err.Error(), http.StatusInternalServerError)
		return
	}
	w.WriteHeader(http.StatusOK)
	json.NewEncoder(w).Encode(map[string]string{"status": "assigned"})
}

func UnassignScanner(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodDelete {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}
	eventID, _ := strconv.Atoi(r.URL.Query().Get("event_id"))
	userID, _ := strconv.Atoi(r.URL.Query().Get("user_id"))

	if err := models.UnassignScanner(eventID, userID); err != nil {
		http.Error(w, "Failed to unassign: "+err.Error(), http.StatusInternalServerError)
		return
	}
	w.WriteHeader(http.StatusOK)
	json.NewEncoder(w).Encode(map[string]string{"status": "unassigned"})
}

func GetEventScanners(w http.ResponseWriter, r *http.Request) {
	eventID, _ := strconv.Atoi(r.URL.Query().Get("event_id"))
	users, err := models.GetEventScanners(eventID)
	if err != nil {
		http.Error(w, err.Error(), http.StatusInternalServerError)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(users)
}

func AssignReseller(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodPost {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}
	var req struct {
		EventID         int     `json:"event_id"`
		UserID          int     `json:"user_id"`
		CommissionType  string  `json:"commission_type"`
		CommissionValue float64 `json:"commission_value"`
	}
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "Invalid body", http.StatusBadRequest)
		return
	}
	// Default validation
	if req.CommissionType == "" {
		req.CommissionType = "fixed"
	}

	if err := models.AssignReseller(req.EventID, req.UserID, req.CommissionType, req.CommissionValue); err != nil {
		http.Error(w, "Failed to assign: "+err.Error(), http.StatusInternalServerError)
		return
	}
	w.WriteHeader(http.StatusOK)
	json.NewEncoder(w).Encode(map[string]string{"status": "assigned"})
}

func UnassignReseller(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodDelete {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}
	eventID, _ := strconv.Atoi(r.URL.Query().Get("event_id"))
	userID, _ := strconv.Atoi(r.URL.Query().Get("user_id"))

	if err := models.UnassignReseller(eventID, userID); err != nil {
		http.Error(w, "Failed to unassign: "+err.Error(), http.StatusInternalServerError)
		return
	}
	w.WriteHeader(http.StatusOK)
	json.NewEncoder(w).Encode(map[string]string{"status": "unassigned"})
}

func GetEventResellers(w http.ResponseWriter, r *http.Request) {
	eventID, _ := strconv.Atoi(r.URL.Query().Get("event_id"))
	resellers, err := models.GetEventResellers(eventID)
	if err != nil {
		http.Error(w, err.Error(), http.StatusInternalServerError)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(resellers)
}
