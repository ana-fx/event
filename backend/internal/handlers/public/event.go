package public

import (
	"encoding/json"
	"event-backend/internal/models"
	"net/http"
)

func ListEvents(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodGet {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}

	events, err := models.GetPublishedEvents()
	if err != nil {
		http.Error(w, "Failed to fetch events: "+err.Error(), http.StatusInternalServerError)
		return
	}

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(events)
}

func GetEvent(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodGet {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}

	slug := r.URL.Query().Get("slug")
	if slug == "" {
		http.Error(w, "Slug required", http.StatusBadRequest)
		return
	}

	event, err := models.GetEventBySlug(slug)
	if err != nil {
		http.Error(w, "Event not found", http.StatusNotFound)
		return
	}

	tickets, err := models.GetTicketsByEventID(event.ID)
	if err != nil {
		// Log error but render event? Or fail? Let's treat as empty tickets for now if fail, or just log.
		// For simplicity, return empty slice if error, or handle error.
		tickets = []models.Ticket{}
	}

	response := struct {
		Event   *models.Event   `json:"event"`
		Tickets []models.Ticket `json:"tickets"`
	}{
		Event:   event,
		Tickets: tickets,
	}

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(response)
}
