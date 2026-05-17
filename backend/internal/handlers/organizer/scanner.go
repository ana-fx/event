package organizer

import (
	"encoding/json"
	"event-backend/internal/models"
	"net/http"
	"strconv"
)

// GetOrganizerEventScanners returns scanners assigned to an event owned by the organizer
func GetOrganizerEventScanners(w http.ResponseWriter, r *http.Request) {
	organizerID, _ := r.Context().Value(models.UserIDKey).(int)
	eventID, _ := strconv.Atoi(r.URL.Query().Get("event_id"))

	if !models.EventBelongsToOrganizer(eventID, organizerID) {
		http.Error(w, "Forbidden", http.StatusForbidden)
		return
	}

	users, err := models.GetEventScanners(eventID)
	if err != nil {
		http.Error(w, err.Error(), http.StatusInternalServerError)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	if users == nil {
		users = []models.User{}
	}
	json.NewEncoder(w).Encode(users)
}

// AssignOrganizerScanner assigns a scanner to an event — only if both belong to the organizer
func AssignOrganizerScanner(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodPost {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}

	organizerID, _ := r.Context().Value(models.UserIDKey).(int)

	var req struct {
		EventID int `json:"event_id"`
		UserID  int `json:"user_id"`
	}
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "Invalid body", http.StatusBadRequest)
		return
	}

	if !models.EventBelongsToOrganizer(req.EventID, organizerID) {
		http.Error(w, "Forbidden: event not yours", http.StatusForbidden)
		return
	}

	if !models.ScannerBelongsToOrganizer(req.UserID, organizerID) {
		http.Error(w, "Forbidden: scanner not yours", http.StatusForbidden)
		return
	}

	if err := models.AssignScanner(req.EventID, req.UserID); err != nil {
		http.Error(w, "Failed to assign: "+err.Error(), http.StatusInternalServerError)
		return
	}

	w.WriteHeader(http.StatusOK)
	json.NewEncoder(w).Encode(map[string]string{"status": "assigned"})
}

// UnassignOrganizerScanner removes a scanner assignment — only if event belongs to organizer
func UnassignOrganizerScanner(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodDelete {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}

	organizerID, _ := r.Context().Value(models.UserIDKey).(int)
	eventID, _ := strconv.Atoi(r.URL.Query().Get("event_id"))
	userID, _ := strconv.Atoi(r.URL.Query().Get("user_id"))

	if !models.EventBelongsToOrganizer(eventID, organizerID) {
		http.Error(w, "Forbidden", http.StatusForbidden)
		return
	}

	if err := models.UnassignScanner(eventID, userID); err != nil {
		http.Error(w, "Failed to unassign: "+err.Error(), http.StatusInternalServerError)
		return
	}

	w.WriteHeader(http.StatusOK)
	json.NewEncoder(w).Encode(map[string]string{"status": "unassigned"})
}
