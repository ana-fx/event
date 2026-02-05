package public

import (
	"encoding/json"
	"event-backend/internal/database"
	"event-backend/internal/models"
	"event-backend/internal/utils"
	"log"
	"net/http"
	"time"
)

type MidtransNotification struct {
	TransactionStatus string `json:"transaction_status"`
	OrderID           string `json:"order_id"` // Matches our Transaction Code (or we used ID?)
	FraudStatus       string `json:"fraud_status"`
}

func PaymentWebhook(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodPost {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}

	var notif MidtransNotification
	if err := json.NewDecoder(r.Body).Decode(&notif); err != nil {
		http.Error(w, "Invalid body", http.StatusBadRequest)
		return
	}

	// 4. Update Transaction Status based on Midtrans response
	var newStatus string
	switch notif.TransactionStatus {
	case "capture":
		switch notif.FraudStatus {
		case "challenge":
			newStatus = "challenge"
		case "accept":
			newStatus = "paid"
		}
	case "settlement":
		newStatus = "paid"
	case "deny":
		newStatus = "failed"
	case "cancel", "expire":
		newStatus = "cancelled"
	case "pending":
		newStatus = "pending"
	default:
		newStatus = "pending"
	}

	// Update Transaction
	query := `UPDATE transactions SET status=$1, updated_at=$2 WHERE code=$3`
	_, err := database.DB.Exec(query, newStatus, time.Now(), notif.OrderID)
	if err != nil {
		log.Printf("Failed to update transaction status: %v", err)
		http.Error(w, "Database error", http.StatusInternalServerError)
		return
	}

	// 5. If Paid, Send Email
	if newStatus == "paid" {
		go func(code string) {
			trx, err := models.GetTransactionByCode(code)
			if err != nil {
				log.Printf("Email error: failed to fetch transaction %s: %v", code, err)
				return
			}

			event, err := models.GetEventByID(trx.EventID)
			if err != nil {
				log.Printf("Email error: failed to fetch event %d: %v", trx.EventID, err)
				return
			}

			body := utils.GetTicketTemplate(trx.Name, event.Name, trx.Code)
			err = utils.SendEmail(trx.Email, "Your Ticket: "+event.Name, body)
			if err != nil {
				log.Printf("Email error: failed to send to %s: %v", trx.Email, err)
			} else {
				log.Printf("Email sent successfully to %s for order %s", trx.Email, code)
			}
		}(notif.OrderID)
	}

	w.WriteHeader(http.StatusOK)
}
