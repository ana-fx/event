package main

import (
	"event-backend/internal/database"
	"event-backend/internal/handlers"
	"event-backend/internal/handlers/admin"
	"event-backend/internal/handlers/public"
	"event-backend/internal/handlers/reseller"
	"event-backend/internal/handlers/scanner"
	"event-backend/internal/middleware"
	"fmt"
	"log"
	"net/http"
	"os"

	"github.com/joho/godotenv"
)

func main() {
	// 0. Load .env
	if err := godotenv.Load(); err != nil {
		log.Println("No .env file found, using defaults if available")
	}

	// 1. Connect to Database
	connStr := os.Getenv("DATABASE_URL")
	if connStr == "" {
		connStr = "postgres://postgres:root@localhost:5432/event_db?sslmode=disable"
	}
	database.Connect(connStr)

	// 2. Routes
	// Public Routes
	http.HandleFunc("/api/events", public.ListEvents)
	http.HandleFunc("/api/events/detail", public.GetEvent) // Query: ?slug=...
	http.HandleFunc("/api/checkout", public.Checkout)
	http.HandleFunc("/api/payment/notification", public.PaymentWebhook)
	http.HandleFunc("/api/banners", public.ListBanners)
	http.HandleFunc("/api/contact", public.SubmitContact)
	http.HandleFunc("/api/transaction/status", public.GetTransactionStatus)

	// Scanner Routes
	http.HandleFunc("/api/scanner/verify", middleware.AuthMiddleware(scanner.Verify))
	http.HandleFunc("/api/scanner/redeem", middleware.AuthMiddleware(scanner.Redeem))

	// Reseller Routes
	http.HandleFunc("/api/reseller/start", middleware.AuthMiddleware(reseller.GetStart))
	http.HandleFunc("/api/reseller/transactions", middleware.AuthMiddleware(reseller.CreateTransaction))

	http.HandleFunc("/api/login", handlers.Login)

	// Admin Routes
	// Dashboard
	http.HandleFunc("/api/admin/dashboard", middleware.AuthMiddleware(admin.DashboardStats))

	// Events
	http.HandleFunc("/api/admin/events", middleware.AuthMiddleware(func(w http.ResponseWriter, r *http.Request) {
		switch r.Method {
		case http.MethodGet:
			admin.ListEvents(w, r)
		case http.MethodPost:
			admin.CreateEvent(w, r)
		case http.MethodPut:
			admin.UpdateEvent(w, r)
		case http.MethodDelete:
			admin.DeleteEvent(w, r)
		default:
			http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		}
	}))

	// Tickets
	http.HandleFunc("/api/admin/tickets", middleware.AuthMiddleware(func(w http.ResponseWriter, r *http.Request) {
		switch r.Method {
		case http.MethodGet:
			admin.ListTickets(w, r)
		case http.MethodPost:
			admin.CreateTicket(w, r)
		case http.MethodPut:
			admin.UpdateTicket(w, r)
		case http.MethodDelete:
			admin.DeleteTicket(w, r)
		default:
			http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		}
	}))

	// Admin User Routes
	http.HandleFunc("/api/admin/users", middleware.AuthMiddleware(func(w http.ResponseWriter, r *http.Request) {
		switch r.Method {
		case http.MethodGet:
			admin.ListUsers(w, r)
		case http.MethodPost:
			admin.CreateUser(w, r)
		case http.MethodPut:
			admin.UpdateUser(w, r)
		case http.MethodDelete:
			admin.DeleteUser(w, r)
		default:
			http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		}
	}))

	// Admin Report Routes
	http.HandleFunc("/api/admin/reports/transactions", middleware.AuthMiddleware(admin.TransactionReport))

	// Banners
	http.HandleFunc("/api/admin/banners", middleware.AuthMiddleware(func(w http.ResponseWriter, r *http.Request) {
		switch r.Method {
		case http.MethodGet:
			admin.ListBanners(w, r)
		case http.MethodPost:
			admin.CreateBanner(w, r)
		case http.MethodPut:
			admin.UpdateBanner(w, r)
		case http.MethodDelete:
			admin.DeleteBanner(w, r)
		default:
			http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		}
	}))

	// Settings
	http.HandleFunc("/api/admin/settings", middleware.AuthMiddleware(func(w http.ResponseWriter, r *http.Request) {
		switch r.Method {
		case http.MethodGet:
			admin.ListSettings(w, r)
		case http.MethodPut:
			admin.UpdateSettings(w, r)
		default:
			http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		}
	}))

	// Withdrawals (Event)
	http.HandleFunc("/api/admin/withdrawals", middleware.AuthMiddleware(func(w http.ResponseWriter, r *http.Request) {
		switch r.Method {
		case http.MethodGet:
			admin.ListWithdrawals(w, r)
		case http.MethodPost:
			admin.CreateWithdrawal(w, r)
		default:
			http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		}
	}))

	// Assignments
	http.HandleFunc("/api/admin/events/assign-scanner", middleware.AuthMiddleware(func(w http.ResponseWriter, r *http.Request) {
		switch r.Method {
		case http.MethodGet:
			admin.GetEventScanners(w, r)
		case http.MethodPost:
			admin.AssignScanner(w, r)
		case http.MethodDelete:
			admin.UnassignScanner(w, r)
		default:
			http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		}
	}))

	http.HandleFunc("/api/admin/events/assign-reseller", middleware.AuthMiddleware(func(w http.ResponseWriter, r *http.Request) {
		switch r.Method {
		case http.MethodGet:
			admin.GetEventResellers(w, r)
		case http.MethodPost:
			admin.AssignReseller(w, r)
		case http.MethodDelete:
			admin.UnassignReseller(w, r)
		default:
			http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		}
	}))

	// Contacts
	http.HandleFunc("/api/admin/contacts", middleware.AuthMiddleware(func(w http.ResponseWriter, r *http.Request) {
		switch r.Method {
		case http.MethodGet:
			admin.ListContacts(w, r)
		case http.MethodDelete:
			admin.DeleteContact(w, r)
		default:
			http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		}
	}))

	// Finance (Deposits)
	http.HandleFunc("/api/admin/finance/deposits", middleware.AuthMiddleware(func(w http.ResponseWriter, r *http.Request) {
		switch r.Method {
		case http.MethodGet:
			admin.GetUserDeposits(w, r)
		case http.MethodPost:
			admin.CreateDeposit(w, r)
		default:
			http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		}
	}))
	http.HandleFunc("/api/admin/finance/balance", middleware.AuthMiddleware(admin.GetResellerBalance))

	// 3. Start Server
	port := ":8080"
	fmt.Printf("Server starting on port %s...\n", port)

	// Wrap the default ServeMux with CORS Middleware
	if err := http.ListenAndServe(port, middleware.CORSMiddleware(http.DefaultServeMux)); err != nil {
		fmt.Printf("Error starting server: %s\n", err)
	}
}
