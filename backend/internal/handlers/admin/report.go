package admin

import (
	"encoding/json"
	"event-backend/internal/models"
	"net/http"
	"strconv"
)

type TicketReport struct {
	TicketID    int     `json:"ticket_id"`
	Name        string  `json:"name"`
	Price       float64 `json:"price"`
	IsActive    bool    `json:"is_active"`
	Volume      int     `json:"volume"`
	Stock       int     `json:"stock"`
	TotalQuota  int     `json:"total_quota"`
	Revenue     float64 `json:"revenue"`
	Saldo       float64 `json:"saldo"`
	OrgTax      float64 `json:"org_tax"`
	HandlingFee float64 `json:"handling_fee"`
	PlatformRev float64 `json:"platform_rev"`
	ServiceFee  float64 `json:"service_fee"`
}

func TransactionReport(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodGet {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}

	eventIDStr := r.URL.Query().Get("event_id")
	eventID := 0
	if eventIDStr != "" {
		var err error
		eventID, err = strconv.Atoi(eventIDStr)
		if err != nil {
			http.Error(w, "Invalid event_id", http.StatusBadRequest)
			return
		}
	}

	// Check if we should filter by organizer
	var organizerIDVal int
	var organizerID *int
	if role, ok := r.Context().Value("userRole").(string); ok && role == "organizer" {
		if uid, ok := r.Context().Value(models.UserIDKey).(int); ok {
			organizerIDVal = uid
			organizerID = &organizerIDVal
		}
	}

	transactions, err := models.GetAllTransactions(eventID, organizerID)
	if err != nil {
		http.Error(w, "Failed to fetch report: "+err.Error(), http.StatusInternalServerError)
		return
	}

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(transactions)
}

func GetTransactionDetail(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodGet {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}

	code := r.URL.Query().Get("code")
	if code == "" {
		http.Error(w, "Transaction code is required", http.StatusBadRequest)
		return
	}

	transaction, err := models.GetTransactionByCode(code)
	if err != nil {
		http.Error(w, "Transaction not found: "+err.Error(), http.StatusNotFound)
		return
	}

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(transaction)
}

func GetEventTicketReport(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodGet {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}

	eventIDStr := r.URL.Query().Get("event_id")
	if eventIDStr == "" {
		http.Error(w, "event_id is required", http.StatusBadRequest)
		return
	}

	eventID, err := strconv.Atoi(eventIDStr)
	if err != nil {
		http.Error(w, "Invalid event_id", http.StatusBadRequest)
		return
	}

	// Check if we should filter by organizer
	var organizerIDVal int
	var organizerID *int
	if role, ok := r.Context().Value("userRole").(string); ok && role == "organizer" {
		if uid, ok := r.Context().Value(models.UserIDKey).(int); ok {
			organizerIDVal = uid
			organizerID = &organizerIDVal
		}
	}

	// 1. Fetch Event for Finance Settings
	event, err := models.GetEventByID(eventID)
	if err != nil {
		http.Error(w, "Failed to fetch event: "+err.Error(), http.StatusInternalServerError)
		return
	}

	// Security check for organizer
	if organizerID != nil && (event.OrganizerID == nil || *event.OrganizerID != *organizerID) {
		http.Error(w, "Forbidden: You do not have access to this event report", http.StatusForbidden)
		return
	}

	// 2. Fetch Tickets for the event
	tickets, err := models.GetTicketsByEventID(eventID)
	if err != nil {
		http.Error(w, "Failed to fetch tickets: "+err.Error(), http.StatusInternalServerError)
		return
	}

	// 3. Fetch Paid Transactions for the event
	transactions, err := models.GetAllTransactions(eventID, organizerID)
	if err != nil {
		http.Error(w, "Failed to fetch transactions: "+err.Error(), http.StatusInternalServerError)
		return
	}

	// Map to aggregate stats per ticket variation
	reportMap := make(map[int]*TicketReport)

	for _, t := range tickets {
		reportMap[t.ID] = &TicketReport{
			TicketID:   t.ID,
			Name:       t.Name,
			Price:      t.Price,
			IsActive:   t.IsActive,
			Stock:      t.Quota,
			TotalQuota: t.Quota, // Assuming Quota is initial quota for now
		}
	}

	// 4. Aggregate Sales
	for _, tx := range transactions {
		if tx.Status != "paid" {
			continue
		}

		for _, item := range tx.Items {
			report, ok := reportMap[item.TicketID]
			if !ok {
				continue
			}

			report.Volume += item.Quantity
			report.Revenue += float64(item.Quantity) * item.Price

			// Deduct from stock
			report.Stock -= item.Quantity
		}
	}

	// 5. Calculate Fees & Saldo
	var finalReports []TicketReport
	for _, t := range tickets {
		r := reportMap[t.ID]

		// Calculations based on event rules
		// Handling Fee (Platform Fee Online) - usually per transaction or % of revenue
		// For simplification in report table variation: revenue * fee / 100
		if event.OrganizerFeeOnlineType == "fixed" {
			// If fixed, it's usually per transaction. But for ticket report, we might need to distribute.
			// Let's assume for this report it's calculated per ticket volume if fixed.
			r.HandlingFee = event.OrganizerFeeOnline * float64(r.Volume)
		} else {
			r.HandlingFee = r.Revenue * (event.OrganizerFeeOnline / 100)
		}

		// Service Fee (Admin Fee)
		if event.AdminFeeType == "fixed" {
			r.ServiceFee = event.AdminFee * float64(r.Volume)
		} else {
			r.ServiceFee = r.Revenue * (event.AdminFee / 100)
		}

		// Org Tax
		if event.OrganizerTaxType == "fixed" {
			r.OrgTax = event.OrganizerTax * float64(r.Volume)
		} else {
			r.OrgTax = r.Revenue * (event.OrganizerTax / 100)
		}

		// Platform Rev (Handling Fee + Service Fee)
		r.PlatformRev = r.HandlingFee + r.ServiceFee

		// Saldo (Revenue - PlatformRev - Org Tax)
		r.Saldo = r.Revenue - r.PlatformRev - r.OrgTax

		finalReports = append(finalReports, *r)
	}

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(finalReports)
}
