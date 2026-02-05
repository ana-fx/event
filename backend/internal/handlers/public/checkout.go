package public

import (
	"bytes"
	"encoding/json"
	"event-backend/internal/models"
	"fmt"
	"io"
	"math/rand"
	"net/http"
	"os"
	"time"
)

// Keys are now in .env, retrieved via os.Getenv()

type CheckoutRequest struct {
	EventID  int    `json:"event_id"`
	TicketID int    `json:"ticket_id"`
	Quantity int    `json:"quantity"`
	Name     string `json:"name"`
	Email    string `json:"email"`
	Phone    string `json:"phone"`
	City     string `json:"city"`
	NIK      string `json:"nik"`
	Gender   string `json:"gender"`
}

func Checkout(w http.ResponseWriter, r *http.Request) {
	defer func() {
		if r := recover(); r != nil {
			fmt.Println("Recovered in Checkout:", r)
			http.Error(w, "Internal Server Error (Panic)", http.StatusInternalServerError)
		}
	}()

	if r.Method != http.MethodPost {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}

	var req CheckoutRequest
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "Invalid body", http.StatusBadRequest)
		return
	}

	// 1. Get Ticket
	ticket, err := models.GetTicketByID(req.TicketID)
	if err != nil {
		http.Error(w, "Ticket not found", http.StatusNotFound)
		return
	}

	// 2. Validate Quota/Max Purchase
	if req.Quantity > ticket.MaxPurchasePerUser {
		http.Error(w, "Quantity exceeds limit per user", http.StatusBadRequest)
		return
	}

	// 3. Calculate Price
	totalPrice := ticket.Price * float64(req.Quantity)

	// 4. Create Transaction in DB first
	code := fmt.Sprintf("INGATE-%d-%d", time.Now().Unix(), rand.Intn(1000))

	trx := models.Transaction{
		Code:       code,
		EventID:    ticket.EventID,
		TicketID:   ticket.ID,
		Name:       req.Name,
		Email:      req.Email,
		Phone:      req.Phone,
		City:       req.City,
		NIK:        req.NIK,
		Gender:     req.Gender,
		Quantity:   req.Quantity,
		TotalPrice: totalPrice,
		Status:     "pending",
	}

	if err := models.CreateTransaction(&trx); err != nil {
		http.Error(w, "Failed to create transaction: "+err.Error(), http.StatusInternalServerError)
		return
	}

	// 5. Call Midtrans Snap Manually
	fmt.Println("Initializing Midtrans (Manual)...")
	apiURL := "https://app.sandbox.midtrans.com/snap/v1/transactions"

	serverKey := os.Getenv("MIDTRANS_SERVER_KEY")
	if serverKey == "" {
		fmt.Println("Error: MIDTRANS_SERVER_KEY is not set")
		http.Error(w, "Payment configuration error", http.StatusInternalServerError)
		return
	}

	snapReq := map[string]interface{}{
		"transaction_details": map[string]interface{}{
			"order_id":     code,
			"gross_amount": int64(totalPrice),
		},
		"customer_details": map[string]interface{}{
			"first_name": req.Name,
			"email":      req.Email,
			"phone":      req.Phone,
		},
		"item_details": []map[string]interface{}{
			{
				"id":       fmt.Sprintf("T-%d", ticket.ID),
				"price":    int64(ticket.Price),
				"quantity": int32(req.Quantity),
				"name":     ticket.Name,
			},
		},
	}

	reqBody, _ := json.Marshal(snapReq)

	client := &http.Client{Timeout: 10 * time.Second}
	sysReq, _ := http.NewRequest("POST", apiURL, bytes.NewBuffer(reqBody))
	sysReq.SetBasicAuth(serverKey, "")
	sysReq.Header.Set("Content-Type", "application/json")
	sysReq.Header.Set("Accept", "application/json")

	fmt.Println("Sending Snap Request:", string(reqBody))
	resp, err := client.Do(sysReq)
	if err != nil {
		fmt.Printf("Midtrans HTTP Error: %v\n", err)
		http.Error(w, "Payment gateway connection failed", http.StatusInternalServerError)
		return
	}
	defer resp.Body.Close()

	bodyBytes, _ := io.ReadAll(resp.Body)
	fmt.Println("Midtrans Response:", string(bodyBytes))

	if resp.StatusCode >= 400 {
		http.Error(w, "Payment gateway returned error: "+string(bodyBytes), http.StatusBadGateway)
		return
	}

	var snapResp struct {
		Token       string `json:"token"`
		RedirectURL string `json:"redirect_url"`
	}
	if err := json.Unmarshal(bodyBytes, &snapResp); err != nil {
		fmt.Println("JSON Unmarshal Error:", err)
		http.Error(w, "Invalid payment response", http.StatusInternalServerError)
		return
	}

	// 6. Update Transaction with Snap Token
	if err := models.UpdateTransactionSnapToken(trx.ID, snapResp.Token, snapResp.RedirectURL); err != nil {
		fmt.Printf("DB Error updating token: %v\n", err)
	}

	trx.SnapToken.String = snapResp.Token
	trx.SnapToken.Valid = true

	w.WriteHeader(http.StatusCreated)
	json.NewEncoder(w).Encode(map[string]interface{}{
		"transaction":  trx,
		"snap_token":   snapResp.Token,
		"redirect_url": snapResp.RedirectURL,
	})
}
