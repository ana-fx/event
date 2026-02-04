package public

import (
	"encoding/json"
	"event-backend/internal/database"
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
	// Assuming OrderID is likely something we can use to look up.
	// In checkout.go we set Code = "ANNTIX-..."
	// We should look up by Code.

	query := `UPDATE transactions SET status=$1, updated_at=$2 WHERE code=$3`
	res, err := database.DB.Exec(query, newStatus, time.Now(), notif.OrderID)
	if err != nil {
		log.Printf("Failed to update transaction status: %v", err)
		http.Error(w, "Database error", http.StatusInternalServerError)
		return
	}

	rows, _ := res.RowsAffected()
	if rows == 0 {
		log.Printf("Transaction not found for order_id: %s", notif.OrderID)
		http.Error(w, "Transaction not found", http.StatusNotFound)
		return
	}

	w.WriteHeader(http.StatusOK)
}
