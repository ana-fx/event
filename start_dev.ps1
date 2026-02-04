$host.ui.RawUI.WindowTitle = "Event Platform Dev Launcher"

Write-Host "Starting Backend Service..." -ForegroundColor Green
Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd backend; go run cmd/api/main.go"

Write-Host "Starting Frontend Service..." -ForegroundColor Cyan
Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd frontend; npm run dev"

Write-Host "Services started in separate windows." -ForegroundColor Yellow
