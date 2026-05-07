package main

import (
	"event-backend/internal/database"
	"event-backend/internal/handlers"
	"event-backend/internal/handlers/admin"
	"event-backend/internal/handlers/public"
	"event-backend/internal/handlers/reseller"
	"event-backend/internal/handlers/scanner"
	"event-backend/internal/middleware"
	"event-backend/internal/utils"
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
		connStr = "postgres://postgres:root@localhost:5432/event_app_db?sslmode=disable"
	}
	database.Connect(connStr)
	database.RunMigrations()

	// 2. Initialize Queue
	utils.InitMailQueue(100, 3)

	// 3. Routes
	// Public Routes
	http.HandleFunc("/api/events", public.ListEvents)
	http.HandleFunc("/api/events/detail", public.GetEvent) // Query: ?slug=...
	http.HandleFunc("/api/checkout", public.Checkout)
	http.HandleFunc("/api/payment/notification", public.PaymentWebhook)
	http.HandleFunc("/api/payment/verify", public.VerifyPayment)
	http.HandleFunc("/api/banners", public.ListBanners)
	http.HandleFunc("/api/contact", public.SubmitContact)
	http.HandleFunc("/api/transaction/status", public.GetTransactionStatus)

	// Scanner Routes (only role: scanner)
	scannerOnly := middleware.RoleMiddleware("scanner")
	http.HandleFunc("/api/scanner/events", middleware.AuthMiddleware(scannerOnly(scanner.GetAssignedEvents)))
	http.HandleFunc("/api/scanner/verify", middleware.AuthMiddleware(scannerOnly(scanner.Verify)))
	http.HandleFunc("/api/scanner/redeem", middleware.AuthMiddleware(scannerOnly(scanner.Redeem)))

	// Reseller Routes
	http.HandleFunc("/api/reseller/start", middleware.AuthMiddleware(reseller.GetStart))
	http.HandleFunc("/api/reseller/transactions", middleware.AuthMiddleware(reseller.CreateTransaction))

	http.HandleFunc("/api/login", handlers.Login)
	http.HandleFunc("/api/me", middleware.AuthMiddleware(handlers.GetMe))

	// Admin Routes
	adminOnly := middleware.RoleMiddleware("admin")

	// Dashboard
	http.HandleFunc("/api/admin/dashboard", middleware.AuthMiddleware(adminOnly(admin.DashboardStats)))

	// Events
	http.HandleFunc("/api/admin/events", middleware.AuthMiddleware(adminOnly(func(w http.ResponseWriter, r *http.Request) {
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
	})))

	// Tickets
	http.HandleFunc("/api/admin/tickets", middleware.AuthMiddleware(adminOnly(func(w http.ResponseWriter, r *http.Request) {
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
	})))

	// Admin User Routes
	http.HandleFunc("/api/admin/users", middleware.AuthMiddleware(adminOnly(func(w http.ResponseWriter, r *http.Request) {
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
	})))

	// Admin Organizer Routes
	http.HandleFunc("/api/admin/organizers", middleware.AuthMiddleware(adminOnly(func(w http.ResponseWriter, r *http.Request) {
		switch r.Method {
		case http.MethodGet:
			admin.ListOrganizers(w, r)
		case http.MethodPost:
			// Using POST for both create and update because of multipart form difficulties with PUT in standard Go http
			if r.URL.Query().Get("id") != "" {
				admin.UpdateOrganizer(w, r)
			} else {
				admin.CreateOrganizer(w, r)
			}
		case http.MethodDelete:
			admin.DeleteOrganizer(w, r)
		default:
			http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		}
	})))

	// Admin Report Routes
	http.HandleFunc("/api/admin/reports/transactions", middleware.AuthMiddleware(adminOnly(admin.TransactionReport)))
	http.HandleFunc("/api/admin/reports/event-tickets", middleware.AuthMiddleware(adminOnly(admin.GetEventTicketReport)))
	http.HandleFunc("/api/admin/reports/detail", middleware.AuthMiddleware(adminOnly(admin.GetTransactionDetail)))

	// Banners
	http.HandleFunc("/api/admin/banners", middleware.AuthMiddleware(adminOnly(func(w http.ResponseWriter, r *http.Request) {
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
	})))

	// Settings
	http.HandleFunc("/api/admin/settings", middleware.AuthMiddleware(adminOnly(func(w http.ResponseWriter, r *http.Request) {
		switch r.Method {
		case http.MethodGet:
			admin.ListSettings(w, r)
		case http.MethodPut:
			admin.UpdateSettings(w, r)
		default:
			http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		}
	})))

	// Withdrawals (Event)
	http.HandleFunc("/api/admin/withdrawals", middleware.AuthMiddleware(adminOnly(func(w http.ResponseWriter, r *http.Request) {
		switch r.Method {
		case http.MethodGet:
			admin.ListWithdrawals(w, r)
		case http.MethodPost:
			admin.CreateWithdrawal(w, r)
		default:
			http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		}
	})))

	// Assignments
	http.HandleFunc("/api/admin/events/assign-scanner", middleware.AuthMiddleware(adminOnly(func(w http.ResponseWriter, r *http.Request) {
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
	})))

	http.HandleFunc("/api/admin/events/assign-reseller", middleware.AuthMiddleware(adminOnly(func(w http.ResponseWriter, r *http.Request) {
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
	})))

	// Contacts
	http.HandleFunc("/api/admin/contacts", middleware.AuthMiddleware(adminOnly(func(w http.ResponseWriter, r *http.Request) {
		switch r.Method {
		case http.MethodGet:
			admin.ListContacts(w, r)
		case http.MethodDelete:
			admin.DeleteContact(w, r)
		default:
			http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		}
	})))

	// Finance (Deposits)
	http.HandleFunc("/api/admin/finance/deposits", middleware.AuthMiddleware(adminOnly(func(w http.ResponseWriter, r *http.Request) {
		switch r.Method {
		case http.MethodGet:
			admin.GetUserDeposits(w, r)
		case http.MethodPost:
			admin.CreateDeposit(w, r)
		default:
			http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		}
	})))
	http.HandleFunc("/api/admin/finance/balance", middleware.AuthMiddleware(adminOnly(admin.GetResellerBalance)))

	// Organizer Routes
	organizerOnly := middleware.RoleMiddleware("organizer", "admin")
	http.HandleFunc("/api/organizer/dashboard", middleware.AuthMiddleware(organizerOnly(admin.DashboardStats)))
	http.HandleFunc("/api/organizer/events", middleware.AuthMiddleware(organizerOnly(func(w http.ResponseWriter, r *http.Request) {
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
	})))
	http.HandleFunc("/api/organizer/tickets", middleware.AuthMiddleware(organizerOnly(func(w http.ResponseWriter, r *http.Request) {
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
	})))
	http.HandleFunc("/api/organizer/reports/transactions", middleware.AuthMiddleware(organizerOnly(admin.TransactionReport)))
	http.HandleFunc("/api/organizer/reports/tickets", middleware.AuthMiddleware(organizerOnly(admin.GetEventTicketReport)))
	http.HandleFunc("/api/organizer/profile", middleware.AuthMiddleware(organizerOnly(handlers.GetOrganizerProfile)))
	http.HandleFunc("/api/organizer/profile/update", middleware.AuthMiddleware(organizerOnly(handlers.UpdateOrganizerProfile)))

	// Static Files
	http.Handle("/uploads/", http.StripPrefix("/uploads/", http.FileServer(http.Dir("storage/uploads"))))

	// 3. Start Server
	port := ":8080"
	fmt.Printf("Server starting on port %s...\n", port)
	// Wrap the default ServeMux with Recovery and CORS Middleware
	handler := middleware.RecoveryMiddleware(middleware.CORSMiddleware(http.DefaultServeMux))
	if err := http.ListenAndServe(port, handler); err != nil {
		fmt.Printf("Error starting server: %s\n", err)
	}
}
