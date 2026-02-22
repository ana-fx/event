package admin

import (
	"encoding/json"
	"event-backend/internal/models"
	"event-backend/internal/utils"
	"net/http"
	"strconv"
	"strings"
	"time"
)

func ListEvents(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodGet {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}

	idStr := r.URL.Query().Get("id")
	if idStr != "" {
		id, err := strconv.Atoi(idStr)
		if err == nil {
			event, err := models.GetEventByID(id)
			if err == nil {
				w.Header().Set("Content-Type", "application/json")
				json.NewEncoder(w).Encode(event)
				return
			}
		}
	}

	// Check if we should filter by organizer
	var organizerID *int
	if role, ok := r.Context().Value("userRole").(string); ok && role == "organizer" {
		if uid, ok := r.Context().Value(models.UserIDKey).(int); ok {
			organizerID = &uid
		}
	}

	events, err := models.GetAllEvents(organizerID)
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

	ytLink := r.FormValue("youtube_link")
	if ytLink != "" {
		req.YoutubeLink = &ytLink
	}

	// Organizer security: if role is organizer, force their own ID
	if role, ok := r.Context().Value("userRole").(string); ok && role == "organizer" {
		if uid, ok := r.Context().Value(models.UserIDKey).(int); ok {
			req.OrganizerID = &uid
		}
	} else {
		// Admin can still set any organizer_id
		if orgIDStr := r.FormValue("organizer_id"); orgIDStr != "" {
			if id, err := strconv.Atoi(orgIDStr); err == nil {
				req.OrganizerID = &id
			}
		}
	}

	req.Status = r.FormValue("status")

	// Finance Fields
	req.OrganizerTax, _ = strconv.ParseFloat(r.FormValue("organizer_tax"), 64)
	req.OrganizerTaxType = r.FormValue("organizer_tax_type")
	if req.OrganizerTaxType == "" {
		req.OrganizerTaxType = "fixed"
	}
	req.AdminFee, _ = strconv.ParseFloat(r.FormValue("admin_fee"), 64)
	req.AdminFeeType = r.FormValue("admin_fee_type")
	if req.AdminFeeType == "" {
		req.AdminFeeType = "fixed"
	}
	req.PPN, _ = strconv.ParseFloat(r.FormValue("ppn"), 64)
	req.PPNType = r.FormValue("ppn_type")
	if req.PPNType == "" {
		req.PPNType = "fixed"
	}

	req.ResellerFeeValue, _ = strconv.ParseFloat(r.FormValue("reseller_fee_value"), 64)
	req.ResellerFeeType = r.FormValue("reseller_fee_type")
	if req.ResellerFeeType == "" {
		req.ResellerFeeType = "fixed"
	}

	req.OrganizerFeeOnline, _ = strconv.ParseFloat(r.FormValue("organizer_fee_online"), 64)
	req.OrganizerFeeOnlineType = r.FormValue("organizer_fee_online_type")
	if req.OrganizerFeeOnlineType == "" {
		req.OrganizerFeeOnlineType = "fixed"
	}

	req.OrganizerFeeReseller, _ = strconv.ParseFloat(r.FormValue("organizer_fee_reseller"), 64)
	req.OrganizerFeeResellerType = r.FormValue("organizer_fee_reseller_type")
	if req.OrganizerFeeResellerType == "" {
		req.OrganizerFeeResellerType = "fixed"
	}

	req.PgFee, _ = strconv.ParseFloat(r.FormValue("pg_fee"), 64)
	req.PgFeeType = r.FormValue("pg_fee_type")
	if req.PgFeeType == "" {
		req.PgFeeType = "fixed"
	}

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
	// Detect content type to choose between Multipart and JSON
	contentType := r.Header.Get("Content-Type")
	if strings.Contains(contentType, "multipart/form-data") {
		if err := r.ParseMultipartForm(10 << 20); err != nil {
			http.Error(w, "Failed to parse form", http.StatusBadRequest)
			return
		}
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
		organizerName := r.FormValue("organizer_name")
		if organizerName != "" {
			req.OrganizerName = &organizerName
		}

		ytLink := r.FormValue("youtube_link")
		if ytLink != "" {
			req.YoutubeLink = &ytLink
		}

		req.Status = r.FormValue("status")

		// Finance Fields
		if val := r.FormValue("organizer_tax"); val != "" {
			req.OrganizerTax, _ = strconv.ParseFloat(val, 64)
		} else {
			req.OrganizerTax = -1 // Sentinel value
		}
		req.OrganizerTaxType = r.FormValue("organizer_tax_type")

		if val := r.FormValue("admin_fee"); val != "" {
			req.AdminFee, _ = strconv.ParseFloat(val, 64)
		} else {
			req.AdminFee = -1
		}
		req.AdminFeeType = r.FormValue("admin_fee_type")

		if val := r.FormValue("ppn"); val != "" {
			req.PPN, _ = strconv.ParseFloat(val, 64)
		} else {
			req.PPN = -1
		}
		req.PPNType = r.FormValue("ppn_type")

		if val := r.FormValue("reseller_fee_value"); val != "" {
			req.ResellerFeeValue, _ = strconv.ParseFloat(val, 64)
		} else {
			req.ResellerFeeValue = -1
		}
		req.ResellerFeeType = r.FormValue("reseller_fee_type")

		if val := r.FormValue("organizer_fee_online"); val != "" {
			req.OrganizerFeeOnline, _ = strconv.ParseFloat(val, 64)
		} else {
			req.OrganizerFeeOnline = -1
		}
		req.OrganizerFeeOnlineType = r.FormValue("organizer_fee_online_type")

		if val := r.FormValue("organizer_fee_reseller"); val != "" {
			req.OrganizerFeeReseller, _ = strconv.ParseFloat(val, 64)
		} else {
			req.OrganizerFeeReseller = -1
		}
		req.OrganizerFeeResellerType = r.FormValue("organizer_fee_reseller_type")

		if val := r.FormValue("pg_fee"); val != "" {
			req.PgFee, _ = strconv.ParseFloat(val, 64)
		} else {
			req.PgFee = -1
		}
		req.PgFeeType = r.FormValue("pg_fee_type")

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

		if val := r.FormValue("organizer_id"); val != "" {
			oid, _ := strconv.Atoi(val)
			req.OrganizerID = &oid
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
		// JSON Update fallback
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
	if req.Status != "" {
		current.Status = req.Status
	}

	// Finance Fields
	if req.OrganizerTax != -1 {
		current.OrganizerTax = req.OrganizerTax
	}
	if req.OrganizerTaxType != "" {
		current.OrganizerTaxType = req.OrganizerTaxType
	}
	if req.AdminFee != -1 {
		current.AdminFee = req.AdminFee
	}
	if req.AdminFeeType != "" {
		current.AdminFeeType = req.AdminFeeType
	}
	if req.PPN != -1 {
		current.PPN = req.PPN
	}
	if req.PPNType != "" {
		current.PPNType = req.PPNType
	}
	if req.ResellerFeeValue != -1 {
		current.ResellerFeeValue = req.ResellerFeeValue
	}
	if req.ResellerFeeType != "" {
		current.ResellerFeeType = req.ResellerFeeType
	}
	if req.OrganizerFeeOnline != -1 {
		current.OrganizerFeeOnline = req.OrganizerFeeOnline
	}
	if req.OrganizerFeeOnlineType != "" {
		current.OrganizerFeeOnlineType = req.OrganizerFeeOnlineType
	}
	if req.OrganizerFeeReseller != -1 {
		current.OrganizerFeeReseller = req.OrganizerFeeReseller
	}
	if req.OrganizerFeeResellerType != "" {
		current.OrganizerFeeResellerType = req.OrganizerFeeResellerType
	}

	// Fields that user might want to clear/empty
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
	if req.YoutubeLink != nil {
		current.YoutubeLink = req.YoutubeLink
	}

	if req.OrganizerID != nil {
		current.OrganizerID = req.OrganizerID
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

	event, err := models.GetEventByID(id)
	if err != nil {
		http.Error(w, "Event not found", http.StatusNotFound)
		return
	}

	// Security check for organizers
	if role, ok := r.Context().Value("userRole").(string); ok && role == "organizer" {
		uid, _ := r.Context().Value(models.UserIDKey).(int)
		if event.OrganizerID == nil || *event.OrganizerID != uid {
			http.Error(w, "Forbidden: You can only delete your own events", http.StatusForbidden)
			return
		}
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
