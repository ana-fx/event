package handlers

import (
	"database/sql"
	"encoding/json"
	"event-backend/internal/models"
	"event-backend/internal/utils"
	"net/http"

	"golang.org/x/crypto/bcrypt"
)

func UpdateOrganizerProfile(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodPost {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}

	// Extract organizerID from context (injected by AuthMiddleware/RoleMiddleware)
	organizerID, ok := r.Context().Value(models.UserIDKey).(int)
	if !ok {
		http.Error(w, "Unauthorized", http.StatusUnauthorized)
		return
	}

	if err := r.ParseMultipartForm(10 << 20); err != nil {
		http.Error(w, "Failed to parse form", http.StatusBadRequest)
		return
	}

	existingUser, err := models.GetUserByID(organizerID)
	if err != nil {
		http.Error(w, "Profile not found", http.StatusNotFound)
		return
	}

	// Update allowed fields
	if name := r.FormValue("name"); name != "" {
		existingUser.Name = name
	}
	if username := r.FormValue("username"); username != "" {
		existingUser.Username = sql.NullString{String: username, Valid: true}
	}
	if organizerName := r.FormValue("organizer_name"); organizerName != "" {
		existingUser.OrganizerName = sql.NullString{String: organizerName, Valid: true}
	}
	if aboutUs := r.FormValue("about_us"); aboutUs != "" {
		existingUser.AboutUs = sql.NullString{String: aboutUs, Valid: true}
	}
	if phone := r.FormValue("phone"); phone != "" {
		existingUser.Phone = sql.NullString{String: phone, Valid: true}
	}
	if address := r.FormValue("address"); address != "" {
		existingUser.Address = sql.NullString{String: address, Valid: true}
	}

	if pwd := r.FormValue("password"); pwd != "" {
		hashed, _ := bcrypt.GenerateFromPassword([]byte(pwd), bcrypt.DefaultCost)
		existingUser.Password = string(hashed)
	} else {
		existingUser.Password = "" // models.UpdateUser will ignore password if empty string
	}

	// Handle Logo Upload
	file, header, err := r.FormFile("logo")
	if err == nil {
		defer file.Close()
		path, err := utils.UploadFile(file, header, "organizers")
		if err == nil {
			existingUser.ProfilePhotoPath = sql.NullString{String: path, Valid: true}
		}
	}

	if err := models.UpdateUser(existingUser); err != nil {
		http.Error(w, "Failed to update profile: "+err.Error(), http.StatusInternalServerError)
		return
	}

	existingUser.Password = ""
	w.WriteHeader(http.StatusOK)
	json.NewEncoder(w).Encode(map[string]interface{}{"status": "updated", "user": existingUser})
}

func GetOrganizerProfile(w http.ResponseWriter, r *http.Request) {
	organizerID, ok := r.Context().Value(models.UserIDKey).(int)
	if !ok {
		http.Error(w, "Unauthorized", http.StatusUnauthorized)
		return
	}

	user, err := models.GetUserByID(organizerID)
	if err != nil {
		http.Error(w, "Profile not found", http.StatusNotFound)
		return
	}

	user.Password = ""
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(user)
}
