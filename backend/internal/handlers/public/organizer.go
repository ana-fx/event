package public

import (
	"database/sql"
	"encoding/json"
	"event-backend/internal/database"
	"net/http"
	"strconv"
)

func GetOrganizerProfile(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodGet {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}

	id, err := strconv.Atoi(r.URL.Query().Get("id"))
	if err != nil || id == 0 {
		http.Error(w, "Invalid id", http.StatusBadRequest)
		return
	}

	var profile struct {
		ID              int            `json:"id"`
		Name            string         `json:"name"`
		OrganizerName   sql.NullString `json:"organizer_name"`
		AboutUs         sql.NullString `json:"about_us"`
		Phone           sql.NullString `json:"phone"`
		Province        sql.NullString `json:"province"`
		City            sql.NullString `json:"city"`
		ProfilePhotoPath sql.NullString `json:"profile_photo_path"`
	}

	err = database.DB.QueryRow(`
		SELECT id, name, organizer_name, about_us, phone, province, city, profile_photo_path
		FROM users
		WHERE id = $1 AND role = 'organizer' AND is_active = true`, id,
	).Scan(
		&profile.ID, &profile.Name, &profile.OrganizerName, &profile.AboutUs,
		&profile.Phone, &profile.Province, &profile.City, &profile.ProfilePhotoPath,
	)
	if err != nil {
		http.Error(w, "Organizer not found", http.StatusNotFound)
		return
	}

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(profile)
}
