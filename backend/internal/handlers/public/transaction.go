package public

import (
	"database/sql"
	"encoding/json"
	"event-backend/internal/database"
	"event-backend/internal/models"
	"fmt"
	"io"
	"net/http"
	"os"
	"time"
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
		TicketPrice            float64 `json:"ticket_price"`
		AdminFee               float64 `json:"admin_fee"`
		AdminFeeType           string  `json:"admin_fee_type"`
		OrganizerFeeOnline     float64 `json:"organizer_fee_online"`
		OrganizerFeeOnlineType string  `json:"organizer_fee_online_type"`
		PPN                    float64 `json:"ppn"`
		PPNType                string  `json:"ppn_type"`
		PgFee                  float64 `json:"pg_fee"`
		PgFeeType              string  `json:"pg_fee_type"`
		SyncDebug              string  `json:"sync_debug,omitempty"`
		EventName              string  `json:"event_name"`
		TicketName             string  `json:"ticket_name"`
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
		LEFT JOIN events e ON t.event_id = e.id
		LEFT JOIN tickets tk ON t.ticket_id = tk.id
		WHERE t.code = $1
	`
	var (
		ticketPrice            sql.NullFloat64
		adminFee               sql.NullFloat64
		adminFeeType           sql.NullString
		organizerFeeOnline     sql.NullFloat64
		organizerFeeOnlineType sql.NullString
		ppn                    sql.NullFloat64
		ppnType                sql.NullString
		pgFee                  sql.NullFloat64
		pgFeeType              sql.NullString
	)

	err := database.DB.QueryRow(query, code).Scan(
		&td.ID, &td.Code, &td.EventID, &td.TicketID, &td.Name, &td.Email, &td.Phone, &td.City, &td.NIK, &td.Gender, &td.Quantity, &td.TotalPrice, &td.Status, &td.CreatedAt,
		&td.SnapToken, &td.RedirectURL,
		&td.EventName,
		&td.TicketName,
		&ticketPrice,
		&adminFee, &adminFeeType,
		&organizerFeeOnline, &organizerFeeOnlineType,
		&ppn, &ppnType,
		&pgFee, &pgFeeType,
	)
	if err != nil {
		fmt.Printf("[ERROR] GetTransactionStatus for %s: %v\n", code, err)
		http.Error(w, "Transaction not found", http.StatusNotFound)
		return
	}

	td.TicketPrice = ticketPrice.Float64
	td.AdminFee = adminFee.Float64
	td.AdminFeeType = adminFeeType.String
	td.OrganizerFeeOnline = organizerFeeOnline.Float64
	td.OrganizerFeeOnlineType = organizerFeeOnlineType.String
	td.PPN = ppn.Float64
	td.PPNType = ppnType.String
	td.PgFee = pgFee.Float64
	td.PgFeeType = pgFeeType.String

	// Midtrans Sync Logic (Local Dev Friendly)
	sync := r.URL.Query().Get("sync") == "true"
	forcePaid := r.URL.Query().Get("force_paid") == "true"

	if (sync || forcePaid) && td.Status == "pending" {
		if forcePaid {
			fmt.Printf("[DEV] Force marking transaction %s as PAID\n", td.Code)
			database.DB.Exec("UPDATE transactions SET status='paid', updated_at=$1 WHERE id=$2", time.Now(), td.ID)
			td.Status = "paid"
		} else {
			serverKey := os.Getenv("MIDTRANS_SERVER_KEY")
			if serverKey != "" {
				isProduction := os.Getenv("MIDTRANS_IS_PRODUCTION") == "true"
				apiURL := fmt.Sprintf("https://api.sandbox.midtrans.com/v2/%s/status", td.Code)
				if isProduction {
					apiURL = fmt.Sprintf("https://api.midtrans.com/v2/%s/status", td.Code)
				}

				req, _ := http.NewRequest("GET", apiURL, nil)
				req.SetBasicAuth(serverKey, "")
				req.Header.Set("Accept", "application/json")

				fmt.Printf("[DEBUG] Syncing %s with Midtrans: %s\n", td.Code, apiURL)

				td.SyncDebug = fmt.Sprintf("Checking %s...", apiURL)
				client := &http.Client{Timeout: 10 * time.Second}
				resp, err := client.Do(req)
				if err != nil {
					fmt.Printf("[ERROR] Midtrans API call failed for %s: %v\n", td.Code, err)
					td.SyncDebug = fmt.Sprintf("Error: %v", err)
				} else {
					defer resp.Body.Close()
					var midtransResp struct {
						TransactionStatus string `json:"transaction_status"`
						StatusCode        string `json:"status_code"`
						StatusMessage     string `json:"status_message"`
					}
					body, _ := io.ReadAll(resp.Body)
					fmt.Printf("[DEBUG] Midtrans Response for %s: %s\n", td.Code, string(body))
					td.SyncDebug = fmt.Sprintf("Response %d: %s", resp.StatusCode, string(body))
					json.Unmarshal(body, &midtransResp)

					if midtransResp.TransactionStatus == "settlement" || midtransResp.TransactionStatus == "capture" || midtransResp.TransactionStatus == "success" {
						fmt.Printf("[SUCCESS] Transaction %s confirmed as PAID by Midtrans\n", td.Code)
						// Update DB to paid
						_, dbErr := database.DB.Exec("UPDATE transactions SET status='paid', updated_at=$1 WHERE id=$2", time.Now(), td.ID)
						if dbErr != nil {
							fmt.Printf("[ERROR] Failed to update DB for %s: %v\n", td.Code, dbErr)
							td.SyncDebug += " | DB Update Failed"
						} else {
							td.Status = "paid"
							td.SyncDebug += " | Status Updated to PAID"
						}
					} else {
						fmt.Printf("[INFO] Transaction %s is still %s according to Midtrans\n", td.Code, midtransResp.TransactionStatus)
						td.SyncDebug += fmt.Sprintf(" | Current status: %s", midtransResp.TransactionStatus)
					}
				}
			} else {
				td.SyncDebug = "Error: MIDTRANS_SERVER_KEY is missing"
			}
		}
	}

	// Load Items
	rows, err := database.DB.Query(`SELECT id, transaction_id, ticket_id, name, price, quantity, created_at FROM transaction_items WHERE transaction_id = $1`, td.ID)
	if err == nil {
		defer rows.Close()
		for rows.Next() {
			var item models.TransactionItem
			if err := rows.Scan(&item.ID, &item.TransactionID, &item.TicketID, &item.Name, &item.Price, &item.Quantity, &item.CreatedAt); err == nil {
				td.Items = append(td.Items, item)
			}
		}
	}

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(td)
}
