package public

import (
	"bytes"
	"database/sql"
	"encoding/json"
	"event-backend/internal/models"
	"fmt"
	"io"
	"math/rand"
	"net/http"
	"os"
	"time"

	"event-backend/internal/utils"
)

// Keys are now in .env, retrieved via os.Getenv()

type CheckoutRequest struct {
	EventID int        `json:"event_id"`
	Items   []CartItem `json:"items"`
	Name    string     `json:"name"`
	Email   string     `json:"email"`
	Phone   string     `json:"phone"`
	City    string     `json:"city"`
	NIK     string     `json:"nik"`
	Gender  string     `json:"gender"`
}

type CartItem struct {
	TicketID int `json:"ticket_id"`
	Quantity int `json:"quantity"`
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
		fmt.Println("[ERROR] Checkout: Invalid body:", err)
		http.Error(w, "Invalid body", http.StatusBadRequest)
		return
	}
	fmt.Printf("[DEBUG] Checkout Request: %+v\n", req)

	if len(req.Items) == 0 {
		fmt.Println("[ERROR] Checkout: No tickets selected")
		http.Error(w, "No tickets selected", http.StatusBadRequest)
		return
	}

	// 1. Get Event for Finance Settings
	event, err := models.GetEventByID(req.EventID)
	if err != nil {
		fmt.Printf("[ERROR] Checkout: Event %d not found: %v\n", req.EventID, err)
		http.Error(w, "Event not found", http.StatusNotFound)
		return
	}

	var transactionItems []models.TransactionItem
	var totalTickets int
	var subtotalFloat float64
	var itemDetails []map[string]interface{}

	// 2. Process Items
	for _, item := range req.Items {
		ticket, err := models.GetTicketByID(item.TicketID)
		if err != nil {
			fmt.Printf("[ERROR] Checkout: Ticket %d not found: %v\n", item.TicketID, err)
			http.Error(w, fmt.Sprintf("Ticket %d not found", item.TicketID), http.StatusNotFound)
			return
		}

		if item.Quantity > ticket.MaxPurchasePerUser {
			http.Error(w, fmt.Sprintf("Quantity for %s exceeds limit per user", ticket.Name), http.StatusBadRequest)
			return
		}

		subtotalFloat += ticket.Price * float64(item.Quantity)
		totalTickets += item.Quantity

		transactionItems = append(transactionItems, models.TransactionItem{
			TicketID: ticket.ID,
			Name:     ticket.Name,
			Price:    ticket.Price,
			Quantity: item.Quantity,
		})

		itemDetails = append(itemDetails, map[string]interface{}{
			"id":       fmt.Sprintf("T-%d", ticket.ID),
			"price":    int64(ticket.Price),
			"quantity": int32(item.Quantity),
			"name":     ticket.Name,
		})
	}

	subtotal := float64(int64(subtotalFloat + 0.5))

	// Service Fee (admin_fee)
	serviceFee := 0.0
	if event.AdminFeeType == "fixed" {
		serviceFee = float64(int64(event.AdminFee*float64(totalTickets) + 0.5))
	} else {
		serviceFee = float64(int64(subtotal*(event.AdminFee/100) + 0.5))
	}

	// PPN
	ppn := 0.0
	if event.PPNType == "fixed" {
		ppn = float64(int64(event.PPN + 0.5))
	} else {
		ppn = float64(int64(subtotal*(event.PPN/100) + 0.5))
	}

	// Platform Fee (organizer_fee_online)
	platformFee := 0.0
	if event.OrganizerFeeOnlineType == "fixed" {
		platformFee = float64(int64(event.OrganizerFeeOnline + 0.5))
	} else {
		platformFee = float64(int64(subtotal*(event.OrganizerFeeOnline/100) + 0.5))
	}

	// Payment Gateway Fee (pg_fee)
	pgFee := 0.0
	if event.PgFeeType == "fixed" {
		pgFee = float64(int64(event.PgFee + 0.5))
	} else {
		pgFee = float64(int64(subtotal*(event.PgFee/100) + 0.5))
	}

	totalPrice := subtotal + serviceFee + ppn + platformFee + pgFee

	// 5. Create Transaction in DB first
	code := fmt.Sprintf("INGATE-%d-%d", time.Now().Unix(), rand.Intn(1000))

	trx := models.Transaction{
		Code:       code,
		EventID:    event.ID,
		TicketID:   sql.NullInt64{Int64: int64(transactionItems[0].TicketID), Valid: true}, // Backward compat for first ticket
		Name:       req.Name,
		Email:      req.Email,
		Phone:      req.Phone,
		City:       req.City,
		NIK:        req.NIK,
		Gender:     req.Gender,
		Quantity:   sql.NullInt64{Int64: int64(totalTickets), Valid: true},
		TotalPrice: totalPrice,
		Status:     "pending",
		Items:      transactionItems,
	}

	if err := models.CreateTransaction(&trx); err != nil {
		fmt.Printf("[ERROR] Failed to create transaction: %v\n", err)
		http.Error(w, "Failed to create transaction: "+err.Error(), http.StatusInternalServerError)
		return
	}
	fmt.Printf("[DEBUG] Transaction created in DB: ID=%d Code=%s\n", trx.ID, trx.Code)

	// 6. Call Midtrans Snap Manually
	serverKey := os.Getenv("MIDTRANS_SERVER_KEY")
	if serverKey == "" {
		fmt.Println("Error: MIDTRANS_SERVER_KEY is not set")
		http.Error(w, "Payment configuration error", http.StatusInternalServerError)
		return
	}

	// Environment Detection
	isProduction := os.Getenv("MIDTRANS_IS_PRODUCTION") == "true"
	apiURL := "https://app.sandbox.midtrans.com/snap/v1/transactions"
	if isProduction {
		apiURL = "https://app.midtrans.com/snap/v1/transactions"
		fmt.Println("[DEBUG] Initializing Midtrans (Production)...")
	} else {
		fmt.Println("[DEBUG] Initializing Midtrans (Sandbox)...")
	}

	// Add Fees to Item Details

	if serviceFee > 0 {
		itemDetails = append(itemDetails, map[string]interface{}{
			"id":       "SERVICE-FEE",
			"price":    int64(serviceFee),
			"quantity": 1,
			"name":     "Service Fee",
		})
	}

	if ppn > 0 {
		itemDetails = append(itemDetails, map[string]interface{}{
			"id":       "PPN",
			"price":    int64(ppn),
			"quantity": 1,
			"name":     "PPN",
		})
	}

	if platformFee > 0 {
		itemDetails = append(itemDetails, map[string]interface{}{
			"id":       "HANDLING-FEE",
			"price":    int64(platformFee),
			"quantity": 1,
			"name":     "Handling Fee",
		})
	}

	if pgFee > 0 {
		itemDetails = append(itemDetails, map[string]interface{}{
			"id":       "PAYMENT-FEE",
			"price":    int64(pgFee),
			"quantity": 1,
			"name":     "Payment Fee",
		})
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
		"item_details": itemDetails,
	}

	reqBody, _ := json.Marshal(snapReq)

	client := &http.Client{Timeout: 10 * time.Second}
	sysReq, _ := http.NewRequest("POST", apiURL, bytes.NewBuffer(reqBody))
	sysReq.SetBasicAuth(serverKey, "")
	sysReq.Header.Set("Content-Type", "application/json")
	sysReq.Header.Set("Accept", "application/json")

	fmt.Printf("[DEBUG] Sending Snap Request: %s\n", string(reqBody))
	resp, err := client.Do(sysReq)
	if err != nil {
		fmt.Printf("[ERROR] Midtrans HTTP Error: %v\n", err)
		http.Error(w, "Payment gateway connection failed", http.StatusInternalServerError)
		return
	}
	defer resp.Body.Close()

	bodyBytes, _ := io.ReadAll(resp.Body)
	fmt.Printf("[DEBUG] Midtrans Response [%d]: %s\n", resp.StatusCode, string(bodyBytes))

	if resp.StatusCode >= 400 {
		fmt.Printf("[ERROR] Midtrans API Error: %s\n", string(bodyBytes))
		w.Header().Set("Content-Type", "application/json")
		w.WriteHeader(http.StatusBadGateway)
		json.NewEncoder(w).Encode(map[string]interface{}{
			"error":  "Payment gateway error",
			"detail": string(bodyBytes),
		})
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

	// 7. Send Payment Required Email via Queue
	go func(t models.Transaction) {
		event, err := models.GetEventByID(t.EventID)
		if err != nil {
			fmt.Printf("Email error: failed to fetch event for %s: %v\n", t.Code, err)
			return
		}

		// Generate payment link (using the code)
		paymentLink := fmt.Sprintf("http://localhost:3000/payment/%s", t.Code)
		totalPriceStr := fmt.Sprintf("IDR %s", formatPrice(t.TotalPrice))

		eventImage := ""
		if event.ThumbnailPath != nil {
			assetURL := os.Getenv("ASSET_URL")
			if assetURL == "" {
				assetURL = "http://localhost:8080" // Fallback for local dev
			}
			eventImage = fmt.Sprintf("%s/%s", assetURL, *event.ThumbnailPath)
		}

		ticketName := transactionItems[0].Name
		if len(transactionItems) > 1 {
			ticketName += fmt.Sprintf(" (+%d others)", len(transactionItems)-1)
		}

		body := utils.GetPaymentRequiredTemplate(t.Name, t.Email, t.Phone, t.NIK, t.Gender, t.City, event.Name, ticketName, int(t.Quantity.Int64), totalPriceStr, paymentLink, eventImage)
		utils.EnqueueEmail(t.Email, "Action Required: Complete Your Payment for "+event.Name, body)
	}(trx)

	w.WriteHeader(http.StatusCreated)
	json.NewEncoder(w).Encode(map[string]interface{}{
		"transaction":  trx,
		"snap_token":   snapResp.Token,
		"redirect_url": snapResp.RedirectURL,
	})
}

// Helper to format price for emails
func formatPrice(price float64) string {
	return fmt.Sprintf("%.0f", price)
}
