package admin

import (
	"encoding/json"
	"event-backend/internal/models"
	"net/http"
)

func DashboardStats(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodGet {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}

	activeEvents, err := models.CountActiveEvents()
	if err != nil {
		http.Error(w, "Failed to count events", http.StatusInternalServerError)
		return
	}

	totalUsers, err := models.CountUsers()
	if err != nil {
		http.Error(w, "Failed to count users", http.StatusInternalServerError)
		return
	}

	revenue, err := models.SumPaidRevenue()
	if err != nil {
		http.Error(w, "Failed to sum revenue", http.StatusInternalServerError)
		return
	}

	stats := map[string]interface{}{
		"active_events": activeEvents,
		"total_users":   totalUsers,
		"total_revenue": revenue,
	}

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(stats)
}
