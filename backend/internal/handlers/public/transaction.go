package public

import (
	"encoding/json"
	"event-backend/internal/database"
	"event-backend/internal/models"
	"net/http"
)

func GetTransactionStatus(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodGet {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}
	code := r.URL.Query().Get("code")
	if code == "" {
		http.Error(w, "Code required", http.StatusBadRequest)
		return
	}

	// Fetch transaction by code with extra details
	type TransactionDetail struct {
		models.Transaction
		EventName              string  `json:"event_name"`
		TicketName             string  `json:"ticket_name"`
		TicketPrice            float64 `json:"ticket_price"`
		AdminFee               float64 `json:"admin_fee"`
		AdminFeeType           string  `json:"admin_fee_type"`
		OrganizerFeeOnline     float64 `json:"organizer_fee_online"`
		OrganizerFeeOnlineType string  `json:"organizer_fee_online_type"`
		PPN                    float64 `json:"ppn"`
		PPNType                string  `json:"ppn_type"`
		PgFee                  float64 `json:"pg_fee"`
		PgFeeType              string  `json:"pg_fee_type"`
	}

	var td TransactionDetail
	query := `
		SELECT 
			t.id, t.code, t.event_id, t.ticket_id, t.name, t.email, t.phone, t.city, t.nik, t.gender, t.quantity, t.total_price, t.status, t.created_at,
			t.snap_token, t.redirect_url,
			e.name as event_name,
			tk.name as ticket_name,
			tk.price as ticket_price,
			e.admin_fee, e.admin_fee_type,
			e.organizer_fee_online, e.organizer_fee_online_type,
			e.ppn, e.ppn_type,
			e.pg_fee, e.pg_fee_type
		FROM transactions t
		JOIN events e ON t.event_id = e.id
		JOIN tickets tk ON t.ticket_id = tk.id
		WHERE t.code = $1
	`
	err := database.DB.QueryRow(query, code).Scan(
		&td.ID, &td.Code, &td.EventID, &td.TicketID, &td.Name, &td.Email, &td.Phone, &td.City, &td.NIK, &td.Gender, &td.Quantity, &td.TotalPrice, &td.Status, &td.CreatedAt,
		&td.SnapToken, &td.RedirectURL,
		&td.EventName,
		&td.TicketName,
		&td.TicketPrice,
		&td.AdminFee, &td.AdminFeeType,
		&td.OrganizerFeeOnline, &td.OrganizerFeeOnlineType,
		&td.PPN, &td.PPNType,
		&td.PgFee, &td.PgFeeType,
	)
	if err != nil {
		http.Error(w, "Transaction not found: "+err.Error(), http.StatusNotFound)
		return
	}

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(td)
}
