package public

import (
	"encoding/json"
	"event-backend/internal/database"
	"event-backend/internal/models"
	"event-backend/internal/utils"
	"fmt"
	"log"
	"net/http"
	"os"
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

			ticketName := "Ticket"
			if len(trx.Items) > 0 {
				ticketName = trx.Items[0].Name
				if len(trx.Items) > 1 {
					ticketName += fmt.Sprintf(" (+%d others)", len(trx.Items)-1)
				}
			} else if trx.TicketID.Valid {
				// Fallback for legacy data
				ticket, _ := models.GetTicketByID(int(trx.TicketID.Int64))
				if ticket != nil {
					ticketName = ticket.Name
				}
			}

			totalPriceStr := fmt.Sprintf("IDR %.0f", trx.TotalPrice)

			eventImage := ""
			if event.ThumbnailPath != nil {
				assetURL := os.Getenv("ASSET_URL")
				if assetURL == "" {
					assetURL = "http://localhost:8080"
				}
				eventImage = fmt.Sprintf("%s/%s", assetURL, *event.ThumbnailPath)
			}

			// 1. Send Consolidated Payment Success & Digital Ticket
			successBody := utils.GetSuccessWithTicketTemplate(trx.Name, trx.Email, trx.Phone, trx.NIK, trx.Gender, trx.City, event.Name, ticketName, int(trx.Quantity.Int64), totalPriceStr, trx.Code, eventImage)
			utils.EnqueueEmail(trx.Email, "Success! Your Ticket for "+event.Name, successBody)
		}(notif.OrderID)
	}

	w.WriteHeader(http.StatusOK)
}
