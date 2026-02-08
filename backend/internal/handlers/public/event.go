package public

import (
	"encoding/json"
	"event-backend/internal/models"
	"fmt"
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
	idStr := r.URL.Query().Get("id")

	var event *models.Event
	var err error

	if idStr != "" {
		var id int
		fmt.Sscanf(idStr, "%d", &id)
		event, err = models.GetEventByID(id)
	} else if slug != "" {
		event, err = models.GetEventBySlug(slug)
	} else {
		http.Error(w, "Slug or ID required", http.StatusBadRequest)
		return
	}

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
