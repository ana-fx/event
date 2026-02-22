package admin

import (
	"database/sql"
	"encoding/json"
	"event-backend/internal/models"
	"event-backend/internal/utils"
	"net/http"
	"strconv"

	"golang.org/x/crypto/bcrypt"
)

func ListOrganizers(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodGet {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}

	users, err := models.GetAllUsers()
	if err != nil {
		http.Error(w, "Failed to fetch organizers: "+err.Error(), http.StatusInternalServerError)
		return
	}

	organizers := []models.User{}
	for _, u := range users {
		if u.Role == "organizer" {
			organizers = append(organizers, u)
		}
	}

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(organizers)
}

func CreateOrganizer(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodPost {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}

	// Handle multipart/form-data for image upload
	if err := r.ParseMultipartForm(10 << 20); err != nil { // 10MB
		http.Error(w, "Failed to parse form", http.StatusBadRequest)
		return
	}

	name := r.FormValue("name")
	email := r.FormValue("email")
	password := r.FormValue("password")
	username := r.FormValue("username")
	organizerName := r.FormValue("organizer_name")
	aboutUs := r.FormValue("about_us")
	phone := r.FormValue("phone")
	province := r.FormValue("province")
	city := r.FormValue("city")
	zipCode := r.FormValue("zip_code")
	address := r.FormValue("address")

	if name == "" || email == "" || password == "" {
		http.Error(w, "Name, Email, and Password required", http.StatusBadRequest)
		return
	}

	hashed, err := bcrypt.GenerateFromPassword([]byte(password), bcrypt.DefaultCost)
	if err != nil {
		http.Error(w, "Failed to hash password", http.StatusInternalServerError)
		return
	}

	user := models.User{
		Name:          name,
		Email:         email,
		Password:      string(hashed),
		Role:          "organizer",
		IsActive:      true,
		Username:      sql.NullString{String: username, Valid: username != ""},
		OrganizerName: sql.NullString{String: organizerName, Valid: organizerName != ""},
		AboutUs:       sql.NullString{String: aboutUs, Valid: aboutUs != ""},
		Phone:         sql.NullString{String: phone, Valid: phone != ""},
		Province:      sql.NullString{String: province, Valid: province != ""},
		City:          sql.NullString{String: city, Valid: city != ""},
		ZipCode:       sql.NullString{String: zipCode, Valid: zipCode != ""},
		Address:       sql.NullString{String: address, Valid: address != ""},
	}

	// Handle Logo Upload
	file, header, err := r.FormFile("logo")
	if err == nil {
		defer file.Close()
		path, err := utils.UploadFile(file, header, "organizers")
		if err != nil {
			http.Error(w, "Failed to upload logo: "+err.Error(), http.StatusInternalServerError)
			return
		}
		user.ProfilePhotoPath = sql.NullString{String: path, Valid: true}
	}

	if err := models.CreateUser(&user); err != nil {
		http.Error(w, "Failed to create organizer: "+err.Error(), http.StatusInternalServerError)
		return
	}

	user.Password = ""
	w.WriteHeader(http.StatusCreated)
	json.NewEncoder(w).Encode(user)
}

func UpdateOrganizer(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodPost { // Using POST for easier multipart handling
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}

	idStr := r.URL.Query().Get("id")
	id, _ := strconv.Atoi(idStr)

	if err := r.ParseMultipartForm(10 << 20); err != nil {
		http.Error(w, "Failed to parse form", http.StatusBadRequest)
		return
	}

	existingUser, err := models.GetUserByID(id)
	if err != nil {
		http.Error(w, "Organizer not found", http.StatusNotFound)
		return
	}

	existingUser.Name = r.FormValue("name")
	existingUser.Email = r.FormValue("email")
	existingUser.IsActive = r.FormValue("is_active") == "true"
	existingUser.Username = sql.NullString{String: r.FormValue("username"), Valid: r.FormValue("username") != ""}
	existingUser.OrganizerName = sql.NullString{String: r.FormValue("organizer_name"), Valid: r.FormValue("organizer_name") != ""}
	existingUser.AboutUs = sql.NullString{String: r.FormValue("about_us"), Valid: r.FormValue("about_us") != ""}
	existingUser.Phone = sql.NullString{String: r.FormValue("phone"), Valid: r.FormValue("phone") != ""}
	existingUser.Province = sql.NullString{String: r.FormValue("province"), Valid: r.FormValue("province") != ""}
	existingUser.City = sql.NullString{String: r.FormValue("city"), Valid: r.FormValue("city") != ""}
	existingUser.ZipCode = sql.NullString{String: r.FormValue("zip_code"), Valid: r.FormValue("zip_code") != ""}
	existingUser.Address = sql.NullString{String: r.FormValue("address"), Valid: r.FormValue("address") != ""}

	if pwd := r.FormValue("password"); pwd != "" {
		hashed, _ := bcrypt.GenerateFromPassword([]byte(pwd), bcrypt.DefaultCost)
		existingUser.Password = string(hashed)
	} else {
		existingUser.Password = "" // Don't update password if empty
	}

	// Handle Logo Upload
	file, header, err := r.FormFile("logo")
	if err == nil {
		defer file.Close()
		path, err := utils.UploadFile(file, header, "organizers")
		if err != nil {
			http.Error(w, "Failed to upload logo: "+err.Error(), http.StatusInternalServerError)
			return
		}
		existingUser.ProfilePhotoPath = sql.NullString{String: path, Valid: true}
	}

	if err := models.UpdateUser(existingUser); err != nil {
		http.Error(w, "Failed to update organizer: "+err.Error(), http.StatusInternalServerError)
		return
	}

	w.WriteHeader(http.StatusOK)
	json.NewEncoder(w).Encode(map[string]string{"status": "updated"})
}

func DeleteOrganizer(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodDelete {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}

	idStr := r.URL.Query().Get("id")
	id, _ := strconv.Atoi(idStr)

	if err := models.DeleteUser(id); err != nil {
		http.Error(w, "Failed to delete: "+err.Error(), http.StatusInternalServerError)
		return
	}

	w.WriteHeader(http.StatusOK)
	json.NewEncoder(w).Encode(map[string]string{"status": "deleted"})
}
