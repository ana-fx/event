# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**INGATE** is a full-stack event ticketing platform with a Go REST API backend and a Next.js frontend. It supports multiple user roles: admin, organizer, scanner, reseller, and public user.

## Development Commands

### Starting the Dev Environment

```bash
# macOS/Linux
bash start_dev.sh

# Windows PowerShell
.\start_dev.ps1
```

Both scripts launch the backend (port 8080) and frontend (port 3000) in separate terminal windows.

### Backend (Go)

```bash
cd backend
go run cmd/api/main.go          # Start API server
go run cmd/migrate/main.go      # Run DB migrations
go run cmd/seed/main.go         # Seed database
go run cmd/reset_db/main.go     # Reset database
go build ./...                  # Build all packages
go test ./...                   # Run all tests
```

### Frontend (Next.js)

```bash
cd frontend
npm run dev      # Development server (port 3000)
npm run build    # Production build
npm run start    # Production server
npm run lint     # ESLint
```

## Architecture

### Request Flow

```
Browser → Next.js (port 3000) → [/api/* rewrite] → Go API (port 8080) → PostgreSQL
```

`next.config.ts` rewrites `/api/*` and `/uploads/*` to `BACKEND_URL` (required env var). The frontend never directly exposes the backend URL to the browser.

### Environment Variables

**`backend/.env`**:
- `DATABASE_URL` — PostgreSQL connection string
- `MIDTRANS_SERVER_KEY` — Payment gateway server key
- `SMTP_*` — Brevo SMTP credentials for email

**`frontend/.env`**:
- `BACKEND_URL` — Internal backend URL for Next.js rewrites (e.g., `http://localhost:8080`)
- `NEXT_PUBLIC_API_URL` — Public-facing API URL used by browser-side code
- `NEXT_PUBLIC_MIDTRANS_CLIENT_KEY` — Midtrans client key for payment UI

### Backend Structure (`backend/`)

- `cmd/api/main.go` — Entry point; registers all routes
- `internal/handlers/` — HTTP handlers grouped by role:
  - `admin/` — Admin-only CRUD for all resources
  - `public/` — Unauthenticated endpoints (events, checkout, payment webhook)
  - `organizer/` — Organizer dashboard, event management, reports
  - `scanner/` — Ticket verify and redeem endpoints
  - `reseller/` — Reseller transaction creation and balance
  - `auth.go` — Login and `/api/me`
- `internal/middleware/` — JWT auth, CORS, role enforcement
- `internal/models/` — Data models
- `internal/database/` — PostgreSQL connection and migration SQL files (Goose)
- `internal/utils/` — Email queue (100 async workers, 3 retries), file uploads

The backend uses **only Go standard library** (`net/http`) — no web framework.

### Frontend Structure (`frontend/src/`)

- `app/` — Next.js App Router pages, organized by role (`admin/`, `organizer/`, `reseller/`, `events/`, `checkout/`)
- `components/` — React components mirroring the same role-based structure; `ui/` for shared primitives
- `lib/axios.ts` — Axios instance configured with base URL and cookie-based JWT injection
- `context/` — React Context providers (auth state, etc.)
- `types/` — TypeScript type definitions

### Authentication

JWT tokens are stored in cookies. The backend middleware validates the token and enforces role-based access. The Axios client in `lib/axios.ts` automatically includes credentials.

### Payment Flow

1. User submits checkout → POST `/api/checkout`
2. Backend creates a transaction and returns a Midtrans `redirect_url` (Snap token)
3. Frontend redirects to Midtrans payment page
4. Midtrans posts webhook to `/api/payment/notification`
5. Backend updates transaction status and triggers confirmation email via the async email queue

### Fee System

Events have a multi-tier fee config stored per event: admin fee, organizer online fee, organizer reseller fee, payment gateway fee, tax, and PPN. These are computed at checkout time.

### Database Migrations

Migrations are managed with **Goose** and SQL files live in `internal/database/`. Run via `go run cmd/migrate/main.go`.

## Key Conventions

- **Path alias**: `@/*` maps to `frontend/src/*`
- **TypeScript strict mode** is enabled; build errors are currently suppressed (`ignoreBuildErrors: true` in `next.config.ts`)
- **Image optimization** is disabled (`unoptimized: true`); images can come from `unsplash.com` or `ingate.id`
- Static uploaded files are served from `backend/storage/uploads/` and proxied via `/uploads/*` rewrite
