# Siskeudes Lite - Startup Script
# PowerShell script to start the application

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Siskeudes Lite - Starting..." -ForegroundColor Yellow
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Check if Docker is running
Write-Host "[1/4] Checking Docker..." -ForegroundColor Green
docker --version
if ($LASTEXITCODE -ne 0) {
    Write-Host "ERROR: Docker is not installed or not running!" -ForegroundColor Red
    Write-Host "Please install Docker Desktop and try again." -ForegroundColor Yellow
    exit 1
}

Write-Host "[2/4] Building Docker containers..." -ForegroundColor Green
docker-compose build

Write-Host "[3/4] Starting Docker containers..." -ForegroundColor Green
docker-compose up -d

Write-Host "[4/4] Waiting for services to be ready..." -ForegroundColor Green
Start-Sleep -Seconds 5

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Application Started Successfully!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Access Points:" -ForegroundColor Yellow
Write-Host "  - Web Application : http://localhost:8080" -ForegroundColor White
Write-Host "  - PHPMyAdmin      : http://localhost:8081" -ForegroundColor White
Write-Host ""
Write-Host "Database Credentials:" -ForegroundColor Yellow
Write-Host "  - Server   : db" -ForegroundColor White
Write-Host "  - Database : siskeudes" -ForegroundColor White
Write-Host "  - Username : siskeudes_user" -ForegroundColor White
Write-Host "  - Password : siskeudes_pass" -ForegroundColor White
Write-Host ""
Write-Host "To view logs, run: docker-compose logs -f" -ForegroundColor Cyan
Write-Host "To stop, run: docker-compose down" -ForegroundColor Cyan
Write-Host ""
