# =====================================================
# Copy Si-RT Documentation to Project Folder
# =====================================================
# Script untuk copy dokumentasi SRS Si-RT ke folder project baru
# Author: Antigravity AI
# Date: 27 Desember 2024
# =====================================================

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Si-RT Documentation Copy Script" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Define paths
$sourcePath = "C:\Users\toro\.gemini\antigravity\brain\8ce28009-9a4a-4b94-9b98-841c01468993"
$destPath = "F:\si-rt"

# Create destination folder if not exists
Write-Host "[1/4] Creating destination folder..." -ForegroundColor Yellow
if (-not (Test-Path $destPath)) {
    New-Item -ItemType Directory -Path $destPath -Force | Out-Null
    Write-Host "  [OK] Folder created: $destPath" -ForegroundColor Green
}
else {
    Write-Host "  [OK] Folder already exists: $destPath" -ForegroundColor Green
}

Write-Host ""

# List of files to copy
$files = @(
    "SRS_Si-RT.md",
    "ENHANCEMENT_SUMMARY.md",
    "implementation_plan.md"
)

# Copy files
Write-Host "[2/4] Copying documentation files..." -ForegroundColor Yellow

$successCount = 0
$failCount = 0

foreach ($file in $files) {
    $sourceFile = Join-Path $sourcePath $file
    $destFile = Join-Path $destPath $file
    
    if (Test-Path $sourceFile) {
        try {
            Copy-Item -Path $sourceFile -Destination $destFile -Force
            $fileSize = (Get-Item $destFile).Length
            $fileSizeKB = [math]::Round($fileSize / 1KB, 2)
            Write-Host "  [OK] Copied: $file ($fileSizeKB KB)" -ForegroundColor Green
            $successCount++
        }
        catch {
            Write-Host "  [ERROR] Failed to copy: $file" -ForegroundColor Red
            Write-Host "    Error: $($_.Exception.Message)" -ForegroundColor Red
            $failCount++
        }
    }
    else {
        Write-Host "  [ERROR] Source file not found: $file" -ForegroundColor Red
        $failCount++
    }
}

Write-Host ""

# Summary
Write-Host "[3/4] Copy Summary:" -ForegroundColor Yellow
Write-Host "  - Files copied successfully: $successCount" -ForegroundColor Green
if ($failCount -gt 0) {
    Write-Host "  - Files failed: $failCount" -ForegroundColor Red
}

Write-Host ""

# List destination folder contents
Write-Host "[4/4] Destination folder contents:" -ForegroundColor Yellow
if (Test-Path $destPath) {
    $items = Get-ChildItem -Path $destPath -Filter "*.md" | Select-Object Name, Length, LastWriteTime
    if ($items) {
        $items | ForEach-Object {
            $sizeKB = [math]::Round($_.Length / 1KB, 2)
            $time = $_.LastWriteTime.ToString("yyyy-MM-dd HH:mm:ss")
            Write-Host "  FILE: $($_.Name) - $sizeKB KB - Modified: $time" -ForegroundColor Cyan
        }
    }
    else {
        Write-Host "  (No .md files found)" -ForegroundColor Gray
    }
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Copy Complete!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Dokumentasi Si-RT tersedia di: $destPath" -ForegroundColor White
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "  1. Review SRS_Si-RT.md untuk requirement lengkap" -ForegroundColor White
Write-Host "  2. Baca ENHANCEMENT_SUMMARY.md untuk quick reference" -ForegroundColor White
Write-Host "  3. Ikuti implementation_plan.md untuk development guide" -ForegroundColor White
Write-Host ""

# Pause to see results
Write-Host "Press any key to exit..." -ForegroundColor Gray
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
