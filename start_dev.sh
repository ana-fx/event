#!/bin/bash

# Function to open a new terminal tab/window and run a command (MacOS specific)
run_in_new_tab() {
    local dir="$1"
    local cmd="$2"
    local title="$3"
    
    osascript -e "tell application \"Terminal\" to do script \"cd '$PWD/$dir' && $cmd\""
}

echo "Starting Event Platform Development Environment..."

# Start Backend
echo "Starting Backend (Go)..."
run_in_new_tab "backend" "go run cmd/api/main.go" "Event Backend"

# Start Frontend (override env to local backend)
echo "Starting Frontend (Next.js)..."
run_in_new_tab "frontend" "BACKEND_URL=http://localhost:8080 NEXT_PUBLIC_API_URL=http://localhost:8080/api NEXT_PUBLIC_STORAGE_URL=http://localhost:8080/uploads npm run dev" "Event Frontend"

echo "Services started in separate Terminal windows."
