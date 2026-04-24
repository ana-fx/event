package public

import (
	"encoding/json"
	"event-backend/internal/database"
	"event-backend/internal/models"
	"fmt"
	"io"
	"net/http"
	"os"
	"time"
)

func VerifyPayment(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodPost {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}

	code := r.URL.Query().Get("code")
	if code == "" {
		http.Error(w, "Missing code", http.StatusBadRequest)
		return
	}

	serverKey := os.Getenv("MIDTRANS_SERVER_KEY")
	isProduction := os.Getenv("MIDTRANS_IS_PRODUCTION") == "true"

	baseURL := "https://api.sandbox.midtrans.com"
	if isProduction {
		baseURL = "https://api.midtrans.com"
	}

	apiURL := fmt.Sprintf("%s/v2/%s/status", baseURL, code)

	req, _ := http.NewRequest("GET", apiURL, nil)
	req.SetBasicAuth(serverKey, "")
	req.Header.Set("Accept", "application/json")

	client := &http.Client{Timeout: 10 * time.Second}
	resp, err := client.Do(req)
	if err != nil {
		http.Error(w, "Failed to contact payment gateway", http.StatusInternalServerError)
		return
	}
	defer resp.Body.Close()

	bodyBytes, _ := io.ReadAll(resp.Body)
	fmt.Printf("[DEBUG] Midtrans verify [%d]: %s\n", resp.StatusCode, string(bodyBytes))

	var result struct {
		TransactionStatus string `json:"transaction_status"`
		FraudStatus       string `json:"fraud_status"`
		OrderID           string `json:"order_id"`
	}
	if err := json.Unmarshal(bodyBytes, &result); err != nil {
		http.Error(w, "Invalid gateway response", http.StatusInternalServerError)
		return
	}

	var newStatus string
	switch result.TransactionStatus {
	case "capture":
		if result.FraudStatus == "accept" {
			newStatus = "paid"
		} else {
			newStatus = "challenge"
		}
	case "settlement":
		newStatus = "paid"
	case "deny":
		newStatus = "failed"
	case "cancel", "expire":
		newStatus = "cancelled"
	default:
		newStatus = "pending"
	}

	_, err = database.DB.Exec(
		`UPDATE transactions SET status=$1, updated_at=$2 WHERE code=$3`,
		newStatus, time.Now(), code,
	)
	if err != nil {
		http.Error(w, "Database error", http.StatusInternalServerError)
		return
	}

	// Send success email
	if newStatus == "paid" {
		go func() {
			trx, err := models.GetTransactionByCode(code)
			if err != nil {
				return
			}
			triggerSuccessEmail(trx)
		}()
	}

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]string{"status": newStatus})
}
