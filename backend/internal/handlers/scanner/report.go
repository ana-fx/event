package scanner

import (
	"encoding/json"
	"event-backend/internal/database"
	"event-backend/internal/models"
	"fmt"
	"net/http"
	"strconv"
)

func GetScanReport(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodGet {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}

	scannerID, ok := r.Context().Value(models.UserIDKey).(int)
	if !ok {
		http.Error(w, "Unauthorized", http.StatusUnauthorized)
		return
	}

	eventID, _ := strconv.Atoi(r.URL.Query().Get("event_id"))

	query := `
		SELECT t.id, t.code, t.name, t.email, t.quantity, t.redeemed_at,
		       COALESCE(tk.name, '') as ticket_name,
		       COALESCE(e.name, '') as event_name
		FROM transactions t
		LEFT JOIN tickets tk ON t.ticket_id = tk.id
		LEFT JOIN events e ON t.event_id = e.id
		WHERE t.redeemed_by = $1
		  AND t.redeemed_at IS NOT NULL`

	args := []interface{}{scannerID}
	if eventID > 0 {
		query += fmt.Sprintf(" AND t.event_id = $%d", len(args)+1)
		args = append(args, eventID)
	}
	query += " ORDER BY t.redeemed_at DESC"

	rows, err := database.DB.Query(query, args...)
	if err != nil {
		http.Error(w, "Database error", http.StatusInternalServerError)
		return
	}
	defer rows.Close()

	type ReportRow struct {
		ID         int    `json:"id"`
		Code       string `json:"code"`
		Name       string `json:"name"`
		Email      string `json:"email"`
		Quantity   int    `json:"quantity"`
		RedeemedAt string `json:"redeemed_at"`
		TicketName string `json:"ticket_name"`
		EventName  string `json:"event_name"`
	}

	var results []ReportRow
	for rows.Next() {
		var row ReportRow
		var qty *int64
		var redeemedAt *string
		if err := rows.Scan(&row.ID, &row.Code, &row.Name, &row.Email, &qty, &redeemedAt, &row.TicketName, &row.EventName); err != nil {
			continue
		}
		if qty != nil {
			row.Quantity = int(*qty)
		}
		if redeemedAt != nil {
			row.RedeemedAt = *redeemedAt
		}
		results = append(results, row)
	}

	if results == nil {
		results = []ReportRow{}
	}

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(results)
}
