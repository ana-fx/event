package admin

import (
	"encoding/json"
	"event-backend/internal/models"
	"event-backend/internal/utils"
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

	// Parse Multipart (for images)
	if err := r.ParseMultipartForm(10 << 20); err != nil {
		http.Error(w, "Failed to parse form", http.StatusBadRequest)
		return
	}

	var req models.Event
	req.Name = r.FormValue("name")
	req.Slug = r.FormValue("slug") // Should auto-generate if empty
	if req.Slug == "" {
		req.Slug = utils.Slugify(req.Name)
	}

	req.Category = r.FormValue("category")
	req.Description = r.FormValue("description")

	terms := r.FormValue("terms")
	if terms != "" {
		req.Terms = &terms
	}

	// Location
	loc := r.FormValue("location")
	if loc != "" {
		req.Location = &loc
	}

	province := r.FormValue("province")
	if province != "" {
		req.Province = &province
	}

	city := r.FormValue("city")
	if city != "" {
		req.City = &city
	}

	zip := r.FormValue("zip")
	if zip != "" {
		req.Zip = &zip
	}

	gmaps := r.FormValue("google_map_embed")
	if gmaps != "" {
		req.GoogleMapEmbed = &gmaps
	}

	// SEO
	seoTitle := r.FormValue("seo_title")
	if seoTitle != "" {
		req.SeoTitle = &seoTitle
	}

	seoDesc := r.FormValue("seo_description")
	if seoDesc != "" {
		req.SeoDescription = &seoDesc
	}

	// Organizer
	orgName := r.FormValue("organizer_name")
	if orgName != "" {
		req.OrganizerName = &orgName
	}

	req.Status = r.FormValue("status")

	// Dates
	layout := "2006-01-02T15:04" // HTML datetime-local format
	// Also support ISO format from DatePicker
	if s := r.FormValue("start_date"); s != "" {
		if t, err := time.Parse(time.RFC3339, s); err == nil {
			req.StartDate = t
		} else {
			req.StartDate, _ = time.Parse(layout, s)
		}
	}
	if s := r.FormValue("end_date"); s != "" {
		if t, err := time.Parse(time.RFC3339, s); err == nil {
			req.EndDate = t
		} else {
			req.EndDate, _ = time.Parse(layout, s)
		}
	}

	// Handle File Uploads
	if file, header, err := r.FormFile("banner"); err == nil {
		defer file.Close()
		path, err := utils.UploadFile(file, header, "events")
		if err == nil {
			req.BannerPath = &path
		}
	}
	if file, header, err := r.FormFile("thumbnail"); err == nil {
		defer file.Close()
		path, err := utils.UploadFile(file, header, "events")
		if err == nil {
			req.ThumbnailPath = &path
		}
	}
	if file, header, err := r.FormFile("organizer_logo"); err == nil {
		defer file.Close()
		path, err := utils.UploadFile(file, header, "events")
		if err == nil {
			req.OrganizerLogoPath = &path
		}
	}

	// Basic validation
	if req.Name == "" {
		http.Error(w, "Name is required", http.StatusBadRequest)
		return
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
	// Parse Multipart for updates (including files)
	if err := r.ParseMultipartForm(10 << 20); err == nil {
		// Form Update
		req.Name = r.FormValue("name")
		req.Slug = r.FormValue("slug")
		req.Category = r.FormValue("category")
		req.Description = r.FormValue("description")

		terms := r.FormValue("terms")
		if terms != "" {
			req.Terms = &terms
		}

		loc := r.FormValue("location")
		if loc != "" {
			req.Location = &loc
		}

		province := r.FormValue("province")
		if province != "" {
			req.Province = &province
		}

		city := r.FormValue("city")
		if city != "" {
			req.City = &city
		}

		zip := r.FormValue("zip")
		if zip != "" {
			req.Zip = &zip
		}

		gmaps := r.FormValue("google_map_embed")
		if gmaps != "" {
			req.GoogleMapEmbed = &gmaps
		}

		// SEO
		seoTitle := r.FormValue("seo_title")
		if seoTitle != "" {
			req.SeoTitle = &seoTitle
		}

		seoDesc := r.FormValue("seo_description")
		if seoDesc != "" {
			req.SeoDescription = &seoDesc
		}

		// Organizer
		orgName := r.FormValue("organizer_name")
		if orgName != "" {
			req.OrganizerName = &orgName
		}

		req.Status = r.FormValue("status")

		layout := "2006-01-02T15:04"
		if s := r.FormValue("start_date"); s != "" {
			if t, err := time.Parse(time.RFC3339, s); err == nil {
				req.StartDate = t
			} else {
				req.StartDate, _ = time.Parse(layout, s)
			}
		}
		if s := r.FormValue("end_date"); s != "" {
			if t, err := time.Parse(time.RFC3339, s); err == nil {
				req.EndDate = t
			} else {
				req.EndDate, _ = time.Parse(layout, s)
			}
		}

		// Handle File Uploads (Optional updates)
		if file, header, err := r.FormFile("banner"); err == nil {
			defer file.Close()
			path, err := utils.UploadFile(file, header, "events")
			if err == nil {
				req.BannerPath = &path
			}
		}
		if file, header, err := r.FormFile("thumbnail"); err == nil {
			defer file.Close()
			path, err := utils.UploadFile(file, header, "events")
			if err == nil {
				req.ThumbnailPath = &path
			}
		}
		if file, header, err := r.FormFile("organizer_logo"); err == nil {
			defer file.Close()
			path, err := utils.UploadFile(file, header, "events")
			if err == nil {
				req.OrganizerLogoPath = &path
			}
		}
	} else {
		// JSON Update fallback (existing logic)
		if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
			http.Error(w, "Invalid body", http.StatusBadRequest)
			return
		}
	}

	req.ID = id
	req.UpdatedAt = time.Now()

	// Need to fetch existing first to preserve non-updated fields?
	// For now, assume model update usually overwrites.
	// Actually, the UpdateEvent SQL query expects all fields.
	// A better approach would be to fetch existing, overlay, then update.
	// But let's check UpdateEvent query again. It updates ALL columns.
	// So if we send a partial struct, many fields will be zeroed out.
	// CRITICAL: We MUST fetch the existing event first.

	current, err := models.GetEventByID(id)
	if err != nil {
		http.Error(w, "Event not found", http.StatusNotFound)
		return
	}

	// Overlay changes
	if req.Name != "" {
		current.Name = req.Name
	}
	if req.Slug != "" {
		current.Slug = req.Slug
	}
	if req.Category != "" {
		current.Category = req.Category
	}
	if req.Description != "" {
		current.Description = req.Description
	}
	if req.Terms != nil {
		current.Terms = req.Terms
	}
	if req.Location != nil {
		current.Location = req.Location
	}
	if req.Province != nil {
		current.Province = req.Province
	}
	if req.City != nil {
		current.City = req.City
	}
	if req.Zip != nil {
		current.Zip = req.Zip
	}
	if req.GoogleMapEmbed != nil {
		current.GoogleMapEmbed = req.GoogleMapEmbed
	}
	if req.SeoTitle != nil {
		current.SeoTitle = req.SeoTitle
	}
	if req.SeoDescription != nil {
		current.SeoDescription = req.SeoDescription
	}
	if req.OrganizerName != nil {
		current.OrganizerName = req.OrganizerName
	}
	if req.Status != "" {
		current.Status = req.Status
	}
	if !req.StartDate.IsZero() {
		current.StartDate = req.StartDate
	}
	if !req.EndDate.IsZero() {
		current.EndDate = req.EndDate
	}
	if req.BannerPath != nil {
		current.BannerPath = req.BannerPath
	}
	if req.ThumbnailPath != nil {
		current.ThumbnailPath = req.ThumbnailPath
	}
	if req.OrganizerLogoPath != nil {
		current.OrganizerLogoPath = req.OrganizerLogoPath
	}

	if err := models.UpdateEvent(current); err != nil {
		http.Error(w, "Failed to update: "+err.Error(), http.StatusInternalServerError)
		return
	}

	w.WriteHeader(http.StatusOK)
	json.NewEncoder(w).Encode(map[string]interface{}{"status": "updated", "event": current})
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
