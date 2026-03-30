<h1 align="center">INGATE — Event Ticketing Platform</h1>

<p align="center">
  A full-stack event ticketing platform supporting multi-role management, online payments, and ticket scanning.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Go-1.25-00ADD8?style=flat-square&logo=go&logoColor=white" />
  <img src="https://img.shields.io/badge/Next.js-16-black?style=flat-square&logo=next.js&logoColor=white" />
  <img src="https://img.shields.io/badge/React-19-61DAFB?style=flat-square&logo=react&logoColor=black" />
  <img src="https://img.shields.io/badge/PostgreSQL-blue?style=flat-square&logo=postgresql&logoColor=white" />
  <img src="https://img.shields.io/badge/Tailwind_CSS-4-38BDF8?style=flat-square&logo=tailwindcss&logoColor=white" />
  <img src="https://img.shields.io/badge/TypeScript-5-3178C6?style=flat-square&logo=typescript&logoColor=white" />
</p>

---

## Features

- **Public** — Browse events, purchase tickets, receive e-tickets via email
- **Admin** — Full control over events, tickets, users, organizers, finances, and platform settings
- **Organizer** — Create and manage events, view sales reports, request withdrawals
- **Reseller** — Sell tickets with commission tracking and balance management
- **Scanner** — Verify and redeem tickets at the gate via barcode/QR scan

## Tech Stack

| Layer | Technology |
|---|---|
| Frontend | Next.js 16, React 19, TypeScript, Tailwind CSS 4 |
| Backend | Go 1.25 (net/http, no framework) |
| Database | PostgreSQL |
| Auth | JWT (stored in cookies) |
| Payments | Midtrans |
| Email | Brevo (SMTP) |

## Architecture

```
Browser → Next.js (port 3000) → [/api/* rewrite] → Go API (port 8080) → PostgreSQL
```

## Running Locally

**Prerequisites:** Go 1.21+, Node.js 18+, PostgreSQL 14+

```bash
git clone https://github.com/ana-fx/event.git
cd event
```

Set up your `.env` files in `backend/` and `frontend/` based on the `.env.example` files, then:

```bash
# Run database migrations
cd backend && go run cmd/migrate/main.go
```

Start both servers:

```bash
# macOS / Linux
bash start_dev.sh

# Windows
.\start_dev.ps1
```

Or manually in two terminals:

```bash
# Terminal 1
cd backend && go run cmd/api/main.go

# Terminal 2
cd frontend && npm install && npm run dev
```

Open **http://localhost:3000**.

## License

MIT
