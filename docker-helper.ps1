# ========================================
# Sikades-Lite - Docker Helper Script
# ========================================
# This script helps run Docker commands even if Docker is not in PATH

Write-Host "============================================" -ForegroundColor Cyan
Write-Host "  SIKADES-LITE - Docker Helper" -ForegroundColor Cyan
Write-Host "============================================" -ForegroundColor Cyan
Write-Host ""

# Add Docker to PATH for this session
$dockerPath = "C:\Program Files\Docker\Docker\resources\bin"
if ($env:PATH -notlike "*$dockerPath*") {
    Write-Host "[INFO] Adding Docker to PATH..." -ForegroundColor Yellow
    $env:PATH += ";$dockerPath"
}

# Verify Docker is available
try {
    $dockerVersion = docker --version
    Write-Host "[OK] Docker detected: $dockerVersion" -ForegroundColor Green
} catch {
    Write-Host "[ERROR] Docker not found! Please start Docker Desktop first." -ForegroundColor Red
    exit 1
}

# Verify Docker Compose
try {
    $composeVersion = docker compose version
    Write-Host "[OK] Docker Compose detected: $composeVersion" -ForegroundColor Green
} catch {
    Write-Host "[ERROR] Docker Compose not found!" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "============================================" -ForegroundColor Cyan
Write-Host "  Available Commands:" -ForegroundColor Cyan
Write-Host "============================================" -ForegroundColor Cyan
Write-Host "1. Start containers      : docker compose up -d" -ForegroundColor White
Write-Host "2. Stop containers       : docker compose down" -ForegroundColor White
Write-Host "3. View logs             : docker compose logs -f" -ForegroundColor White
Write-Host "4. Check status          : docker compose ps" -ForegroundColor White
Write-Host "5. Restart containers    : docker compose restart" -ForegroundColor White
Write-Host "6. Enter PHP container   : docker exec -it sikades_lite-app-1 bash" -ForegroundColor White
Write-Host "7. Run migrations        : docker exec sikades_lite-app-1 php spark migrate" -ForegroundColor White
Write-Host "8. View container status : docker ps" -ForegroundColor White
Write-Host ""

# Show menu
Write-Host "What would you like to do?" -ForegroundColor Yellow
Write-Host "[1] Start containers (docker compose up -d)"
Write-Host "[2] Stop containers (docker compose down)"
Write-Host "[3] View container status (docker ps)"
Write-Host "[4] View logs (docker compose logs)"
Write-Host "[5] Custom command"
Write-Host "[0] Exit"
Write-Host ""

$choice = Read-Host "Enter your choice (0-5)"

switch ($choice) {
    "1" {
        Write-Host "[INFO] Starting containers..." -ForegroundColor Yellow
        docker compose up -d
        Write-Host ""
        Write-Host "[INFO] Containers started! Access app at: http://localhost:8080" -ForegroundColor Green
    }
    "2" {
        Write-Host "[INFO] Stopping containers..." -ForegroundColor Yellow
        docker compose down
        Write-Host "[INFO] Containers stopped!" -ForegroundColor Green
    }
    "3" {
        Write-Host "[INFO] Container status:" -ForegroundColor Yellow
        docker ps
    }
    "4" {
        Write-Host "[INFO] Showing logs (Press Ctrl+C to exit)..." -ForegroundColor Yellow
        docker compose logs -f
    }
    "5" {
        $customCmd = Read-Host "Enter docker command (e.g., 'docker compose ps')"
        Invoke-Expression $customCmd
    }
    "0" {
        Write-Host "[INFO] Goodbye!" -ForegroundColor Green
        exit 0
    }
    default {
        Write-Host "[ERROR] Invalid choice!" -ForegroundColor Red
        exit 1
    }
}

Write-Host ""
Write-Host "============================================" -ForegroundColor Cyan
Write-Host "  Done!" -ForegroundColor Cyan
Write-Host "============================================" -ForegroundColor Cyan
