package scanner

import (
	"encoding/json"
	"event-backend/internal/database"
	"event-backend/internal/models"
	"net/http"
	"time"
)

type AssignedEvent struct {
	ID          int        `json:"id"`
	Name        string     `json:"name"`
	BannerPath  *string    `json:"banner_path"`
	StartDate   time.Time  `json:"start_date"`
	EndDate     time.Time  `json:"end_date"`
	Location    *string    `json:"location"`
	City        *string    `json:"city"`
	Status      string     `json:"status"`
}

func GetAssignedEvents(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodGet {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}

	scannerID, ok := r.Context().Value(models.UserIDKey).(int)
	if !ok {
		http.Error(w, "Unauthorized", http.StatusUnauthorized)
		return
	}

	rows, err := database.DB.Query(`
		SELECT e.id, e.name, e.banner_path, e.start_date, e.end_date, e.location, e.city, e.status
		FROM events e
		JOIN event_scanner es ON e.id = es.event_id
		WHERE es.user_id = $1
		ORDER BY e.start_date DESC
	`, scannerID)
	if err != nil {
		http.Error(w, "Database error: "+err.Error(), http.StatusInternalServerError)
		return
	}
	defer rows.Close()

	events := []AssignedEvent{}
	for rows.Next() {
		var e AssignedEvent
		if err := rows.Scan(&e.ID, &e.Name, &e.BannerPath, &e.StartDate, &e.EndDate, &e.Location, &e.City, &e.Status); err != nil {
			http.Error(w, "Scan error: "+err.Error(), http.StatusInternalServerError)
			return
		}
		events = append(events, e)
	}

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(events)
}
