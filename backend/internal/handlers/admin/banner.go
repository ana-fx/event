package admin

import (
	"encoding/json"
	"event-backend/internal/models"
	"event-backend/internal/utils"
	"net/http"
	"strconv"
	"time"
)

func ListBanners(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodGet {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}
	banners, err := models.GetAllBanners()
	if err != nil {
		http.Error(w, err.Error(), http.StatusInternalServerError)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(banners)
}

func CreateBanner(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodPost {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}

	// Parse Multipart Form
	if err := r.ParseMultipartForm(10 << 20); err != nil { // 10MB limit
		http.Error(w, "Failed to parse form", http.StatusBadRequest)
		return
	}

	var b models.Banner
	b.Title = r.FormValue("title")
	b.LinkURL = r.FormValue("link_url")
	b.Slug = r.FormValue("slug") // Should automate if empty?
	if b.Slug == "" {
		b.Slug = "banner-" + strconv.FormatInt(time.Now().Unix(), 10)
	}

	// Handle Image Upload
	file, header, err := r.FormFile("image")
	if err == nil {
		defer file.Close()
		path, err := utils.UploadFile(file, header, "banners")
		if err != nil {
			http.Error(w, "Failed to upload image: "+err.Error(), http.StatusInternalServerError)
			return
		}
		b.ImagePath = path
	} else if err != http.ErrMissingFile {
		http.Error(w, "Error retrieving file", http.StatusBadRequest)
		return
	}

	if b.Title == "" {
		http.Error(w, "Title is required", http.StatusBadRequest)
		return
	}

	b.IsActive = true // Default active

	if err := models.CreateBanner(&b); err != nil {
		http.Error(w, "Failed to create: "+err.Error(), http.StatusInternalServerError)
		return
	}

	w.WriteHeader(http.StatusCreated)
	json.NewEncoder(w).Encode(b)
}

func UpdateBanner(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodPut { // Allow PUT
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}
	idStr := r.URL.Query().Get("id")
	id, _ := strconv.Atoi(idStr)
	var b models.Banner
	if err := json.NewDecoder(r.Body).Decode(&b); err != nil {
		http.Error(w, "Invalid body", http.StatusBadRequest)
		return
	}
	b.ID = id
	if err := models.UpdateBanner(&b); err != nil {
		http.Error(w, "Failed to update: "+err.Error(), http.StatusInternalServerError)
		return
	}
	w.WriteHeader(http.StatusOK)
	json.NewEncoder(w).Encode(map[string]string{"status": "updated"})
}

func DeleteBanner(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodDelete {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}
	idStr := r.URL.Query().Get("id")
	id, _ := strconv.Atoi(idStr)
	if err := models.DeleteBanner(id); err != nil {
		http.Error(w, "Failed to delete: "+err.Error(), http.StatusInternalServerError)
		return
	}
	w.WriteHeader(http.StatusOK)
	json.NewEncoder(w).Encode(map[string]string{"status": "deleted"})
}
