package admin

import (
	"encoding/json"
	"event-backend/internal/models"
	"fmt"
	"net/http"
)

func DashboardStats(w http.ResponseWriter, r *http.Request) {
	fmt.Printf("[DashboardStats] Entry: %s %s from %s\n", r.Method, r.URL.Path, r.RemoteAddr)
	if r.Method != http.MethodGet {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}

	// Check if we should filter by organizer
	var organizerID *int
	if role, ok := r.Context().Value("userRole").(string); ok && role == "organizer" {
		if uid, ok := r.Context().Value(models.UserIDKey).(int); ok {
			organizerID = &uid
		}
	}

	activeEvents, err := models.CountActiveEvents(organizerID)
	if err != nil {
		fmt.Printf("[DashboardStats] Error counting events: %v (organizerID: %v)\n", err, organizerID)
		http.Error(w, "Failed to count events", http.StatusInternalServerError)
		return
	}

	// Admin sees total users, organizer might not need this or see 0
	totalUsers := 0
	if organizerID == nil {
		totalUsers, err = models.CountUsers()
		if err != nil {
			fmt.Printf("[DashboardStats] Error counting users: %v\n", err)
			http.Error(w, "Failed to count users", http.StatusInternalServerError)
			return
		}
	}

	revenue, err := models.SumPaidRevenue(organizerID)
	if err != nil {
		fmt.Printf("[DashboardStats] Error summing revenue: %v (organizerID: %v)\n", err, organizerID)
		http.Error(w, "Failed to sum revenue", http.StatusInternalServerError)
		return
	}

	ticketsSold, err := models.CountPaidTickets(organizerID)
	if err != nil {
		fmt.Printf("[DashboardStats] Error counting tickets sold: %v (organizerID: %v)\n", err, organizerID)
		http.Error(w, "Failed to count tickets sold", http.StatusInternalServerError)
		return
	}

	stats := map[string]interface{}{
		"active_events": activeEvents,
		"total_users":   totalUsers,
		"total_revenue": revenue,
		"tickets_sold":  ticketsSold,
	}

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(stats)
}
