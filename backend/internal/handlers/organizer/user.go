package organizer

import (
	"database/sql"
	"encoding/json"
	"event-backend/internal/models"
	"net/http"
	"strconv"

	"golang.org/x/crypto/bcrypt"
)

// ListScanners returns all scanners created by the authenticated organizer
func ListScanners(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodGet {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}
	organizerID, _ := r.Context().Value(models.UserIDKey).(int)
	scanners, err := models.GetScannersByCreator(organizerID)
	if err != nil {
		http.Error(w, "Failed to fetch scanners: "+err.Error(), http.StatusInternalServerError)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	if scanners == nil {
		scanners = []models.User{}
	}
	json.NewEncoder(w).Encode(scanners)
}

// CreateScanner creates a scanner account owned by the authenticated organizer
func CreateScanner(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodPost {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}

	organizerID, _ := r.Context().Value(models.UserIDKey).(int)

	var req models.User
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "Invalid body", http.StatusBadRequest)
		return
	}

	if req.Name == "" || req.Email == "" || req.Password == "" {
		http.Error(w, "Name, Email, and Password required", http.StatusBadRequest)
		return
	}

	req.Role = "scanner"
	req.IsActive = true
	req.CreatedBy = sql.NullInt64{Int64: int64(organizerID), Valid: true}

	hashed, err := bcrypt.GenerateFromPassword([]byte(req.Password), bcrypt.DefaultCost)
	if err != nil {
		http.Error(w, "Failed to hash password", http.StatusInternalServerError)
		return
	}
	req.Password = string(hashed)

	if err := models.CreateUser(&req); err != nil {
		http.Error(w, "Failed to create scanner: "+err.Error(), http.StatusInternalServerError)
		return
	}

	req.Password = ""
	w.WriteHeader(http.StatusCreated)
	json.NewEncoder(w).Encode(req)
}

// DeleteScanner deletes a scanner account if it was created by the organizer
func DeleteScanner(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodDelete {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}

	organizerID, _ := r.Context().Value(models.UserIDKey).(int)
	scannerID, _ := strconv.Atoi(r.URL.Query().Get("id"))

	if !models.ScannerBelongsToOrganizer(scannerID, organizerID) {
		http.Error(w, "Forbidden", http.StatusForbidden)
		return
	}

	if err := models.DeleteUser(scannerID); err != nil {
		http.Error(w, "Failed to delete: "+err.Error(), http.StatusInternalServerError)
		return
	}

	w.WriteHeader(http.StatusOK)
	json.NewEncoder(w).Encode(map[string]string{"status": "deleted"})
}
