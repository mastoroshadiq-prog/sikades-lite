# Install Google Cloud CLI for Windows
# Panduan Instalasi

## Method 1: Download Installer (RECOMMENDED - Paling Mudah)

1. Download installer dari:
   https://cloud.google.com/sdk/docs/install#windows

2. Jalankan installer yang di-download
   - File: GoogleCloudSDKInstaller.exe
   - Follow wizard (Next, Next, Install)
   - Checklist semua options saat install

3. Setelah install, RESTART Terminal/PowerShell Anda

4. Verify installation:
   gcloud --version

---

## Method 2: Install via Chocolatey (Jika punya Chocolatey)

```powershell
# Install Chocolatey dulu (jika belum ada)
Set-ExecutionPolicy Bypass -Scope Process -Force
[System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072
iex ((New-Object System.Net.WebClient).DownloadString('https://community.chocolatey.org/install.ps1'))

# Install Google Cloud SDK
choco install gcloudsdk -y

# Restart terminal, lalu verify
gcloud --version
```

---

## Method 3: Manual Install via PowerShell

```powershell
# Download installer
$installerUrl = "https://dl.google.com/dl/cloudsdk/channels/rapid/GoogleCloudSDKInstaller.exe"
$installerPath = "$env:TEMP\GoogleCloudSDKInstaller.exe"

# Download
Invoke-WebRequest -Uri $installerUrl -OutFile $installerPath

# Run installer
Start-Process -FilePath $installerPath -Wait

# Restart terminal setelah selesai
```

---

## RECOMMENDED ACTION:

**Gunakan Method 1** (Download Installer):
1. Klik link ini: https://cloud.google.com/sdk/docs/install#windows
2. Click "Download the Cloud SDK installer"
3. Run installer
4. Restart terminal
5. Kembali ke sini

Atau saya bisa download dan run installer otomatis untuk Anda?
