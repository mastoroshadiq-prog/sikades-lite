# API DOCUMENTATION
## SIKADES API Gateway v1.0

---

**Base URL:** `https://api.sikades.id/v1`  
**Protocol:** HTTPS only  
**Format:** JSON  
**Authentication:** Bearer Token (JWT)  

---

## 📋 TABLE OF CONTENTS

1. [Overview](#1-overview)
2. [Authentication](#2-authentication)
3. [API Endpoints](#3-api-endpoints)
4. [Data Models](#4-data-models)
5. [Error Handling](#5-error-handling)
6. [Rate Limiting](#6-rate-limiting)
7. [Pagination](#7-pagination)
8. [Filtering & Sorting](#8-filtering--sorting)
9. [Webhooks](#9-webhooks)
10. [SDK & Libraries](#10-sdk--libraries)

---

## 1. OVERVIEW

### 1.1 API Design Principles

- **RESTful**: Resource-based URLs dengan HTTP verbs standard
- **Stateless**: Setiap request independen (no server-side session)
- **Versioned**: URL includes version (`/v1/`)
- **JSON**: Request & response dalam JSON format
- **HATEOAS**: Hypermedia links untuk navigasi (optional)

### 1.2 Environment URLs

| Environment | Base URL | Purpose |
|-------------|----------|---------|
| Development | `https://dev-api.sikades.id/v1` | Testing internal |
| Staging | `https://staging-api.sikades.id/v1` | UAT |
| Production | `https://api.sikades.id/v1` | Live |

### 1.3 Tech Stack Recommendation

**RECOMMENDED: NestJS**

```bash
# Setup NestJS project
npm i -g @nestjs/cli
nestjs new sikades-api-gateway
cd sikades-api-gateway

# Install dependencies
npm install @nestjs/swagger @nestjs/jwt @nestjs/passport
npm install passport passport-jwt bcrypt
npm install pg typeorm @nestjs/typeorm  # PostgreSQL
npm install redis @nestjs/bull  # Queue
npm install class-validator class-transformer
```

**Project Structure:**
```
sikades-api-gateway/
├── src/
│   ├── main.ts
│   ├── app.module.ts
│   ├── auth/
│   │   ├── auth.module.ts
│   │   ├── auth.controller.ts
│   │   ├── auth.service.ts
│   │   ├── jwt.strategy.ts
│   │   └── dto/
│   ├── dashboard/
│   │   ├── desa/
│   │   ├── kecamatan/
│   │   ├── kabupaten/
│   │   └── provinsi/
│   ├── common/
│   │   ├── decorators/
│   │   ├── guards/
│   │   ├── interceptors/
│   │   └── filters/
│   └── database/
│       ├── migrations/
│       └── seeds/
├── test/
├── .env.example
├── nest-cli.json
├── package.json
└── README.md
```

---

## 2. AUTHENTICATION

### 2.1 Login

**Endpoint:** `POST /auth/login`

**Request:**
```json
{
  "username": "admin_desa",
  "password": "SecurePass123!",
  "device_id": "550e8400-e29b-41d4-a716-446655440000"
}
```

**Response (Success - 200):**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "refresh_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "token_type": "Bearer",
    "expires_in": 900,
    "user": {
      "id": 1,
      "username": "admin_desa",
      "email": "admin@desalembang.id",
      "role": "kepala_desa",
      "level": "desa",
      "kode_desa": "3216012001",
      "nama_desa": "Lembang",
      "kode_kecamatan": "321601",
      "nama_kecamatan": "Lembang",
      "kode_kabupaten": "3216",
      "nama_kabupaten": "Bandung Barat",
      "kode_provinsi": "32",
      "nama_provinsi": "Jawa Barat",
      "photo_url": "https://cdn.sikades.id/avatars/001.jpg",
      "created_at": "2024-01-15T10:30:00Z",
      "last_login_at": "2024-12-26T08:00:00Z"
    }
  }
}
```

**Response (Error - 401):**
```json
{
  "success": false,
  "message": "Invalid credentials",
  "errors": {
    "code": "INVALID_CREDENTIALS",
    "details": "Username or password is incorrect"
  }
}
```

**cURL Example:**
```bash
curl -X POST https://api.sikades.id/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "username": "admin_desa",
    "password": "SecurePass123!",
    "device_id": "550e8400-e29b-41d4-a716-446655440000"
  }'
```

### 2.2 Refresh Token

**Endpoint:** `POST /auth/refresh`

**Request:**
```json
{
  "refresh_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "expires_in": 900
  }
}
```

### 2.3 Logout

**Endpoint:** `POST /auth/logout`

**Headers:**
```
Authorization: Bearer {access_token}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Logout successful"
}
```

### 2.4 Request OTP

**Endpoint:** `POST /auth/otp/request`

**Request:**
```json
{
  "phone": "+6281234567890"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "OTP sent to +6281234567890",
  "data": {
    "otp_id": "otp_123456",
    "expires_in": 300
  }
}
```

### 2.5 Verify OTP

**Endpoint:** `POST /auth/otp/verify`

**Request:**
```json
{
  "otp_id": "otp_123456",
  "otp_code": "123456"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "OTP verified",
  "data": {
    "verified": true
  }
}
```

---

## 3. API ENDPOINTS

### 3.1 Dashboard Desa

#### 3.1.1 Get Dashboard Overview

**Endpoint:** `GET /dashboard/desa/{kode_desa}`

**Headers:**
```
Authorization: Bearer {access_token}
```

**Parameters:**
- `kode_desa` (path, required): Kode desa 13 digit

**Response (200):**
```json
{
  "success": true,
  "data": {
    "kode_desa": "3216012001",
    "nama_desa": "Lembang",
    "tahun": 2024,
    "updated_at": "2024-12-26T08:00:00Z",
    "keuangan": {
      "total_anggaran": 2500000000,
      "realisasi_pendapatan": 2300000000,
      "realisasi_pendapatan_persen": 92.0,
      "realisasi_belanja": 1850000000,
      "realisasi_belanja_persen": 74.0,
      "saldo_kas": 450000000,
      "spp_pending": {
        "count": 3,
        "total_amount": 45000000
      },
      "trend": "up",
      "status": "green"
    },
    "demografi": {
      "total_penduduk": 5432,
      "laki_laki": 2715,
      "perempuan": 2717,
      "jumlah_kk": 1632,
      "mutasi_bulan_ini": {
        "lahir": 5,
        "mati": 2,
        "pindah": 3,
        "datang": 1
      }
    },
    "pembangunan": {
      "total_proyek": 12,
      "selesai": 5,
      "berjalan": 6,
      "belum_mulai": 1,
      "mangkrak": 0,
      "total_anggaran": 850000000,
      "realisasi": 420000000,
      "realisasi_persen": 49.4
    },
    "kesehatan": {
      "balita_total": 342,
      "balita_stunting": 15,
      "stunting_persen": 4.4,
      "ibu_hamil": 23,
      "ibu_hamil_risti": 3,
      "risti_persen": 13.0,
      "coverage_imunisasi_persen": 87.0,
      "posyandu_aktif": 3
    },
    "pelayanan": {
      "surat_bulan_ini": 47,
      "by_kategori": {
        "keterangan": 28,
        "pengantar": 15,
        "lainnya": 4
      },
      "avg_processing_time_minutes": 12,
      "satisfaction_score": 4.7
    }
  }
}
```

#### 3.1.2 Get Keuangan Detail

**Endpoint:** `GET /dashboard/desa/{kode_desa}/keuangan`

**Query Parameters:**
- `tahun` (optional): Tahun anggaran, default current year
- `start_date` (optional): Format YYYY-MM-DD
- `end_date` (optional): Format YYYY-MM-DD

**Response (200):**
```json
{
  "success": true,
  "data": {
    "summary": {
      "total_anggaran": 2500000000,
      "realisasi_pendapatan": 2300000000,
      "realisasi_belanja": 1850000000,
      "saldo_kas": 450000000
    },
    "by_bidang": [
      {
        "kode_bidang": "4.1",
        "nama_bidang": "Pendapatan Asli Desa",
        "anggaran": 50000000,
        "realisasi": 45000000,
        "persen": 90.0
      },
      {
        "kode_bidang": "5.1",
        "nama_bidang": "Belanja Bidang Penyelenggaraan Pemerintahan Desa",
        "anggaran": 800000000,
        "realisasi": 650000000,
        "persen": 81.25
      }
    ],
    "trend_bulanan": [
      {
        "bulan": "2024-01",
        "pendapatan": 150000000,
        "belanja": 120000000,
        "saldo": 30000000
      },
      {
        "bulan": "2024-02",
        "pendapatan": 180000000,
        "belanja": 145000000,
        "saldo": 65000000
      }
      // ... 10 bulan lagi
    ],
    "top_belanja": [
      {
        "uraian": "Belanja Pegawai",
        "amount": 450000000,
        "persen": 24.3
      },
      {
        "uraian": "Belanja Barang dan Jasa",
        "amount": 380000000,
        "persen": 20.5
      }
    ]
  }
}
```

#### 3.1.3 Get BKU Transactions

**Endpoint:** `GET /dashboard/desa/{kode_desa}/bku`

**Query Parameters:**
- `page` (default: 1)
- `limit` (default: 50, max: 500)
- `start_date` (format: YYYY-MM-DD)
- `end_date` (format: YYYY-MM-DD)
- `jenis` (enum: `pendapatan`, `belanja`)
- `rekening_id` (filter by rekening)

**Response (200):**
```json
{
  "success": true,
  "data": {
    "items": [
      {
        "id": 1234,
        "tanggal": "2024-12-20",
        "nomor_bukti": "BKU/001/XII/2024",
        "kode_rekening": "4.1.1.01",
        "nama_rekening": "Hasil Usaha Desa - Penyewaan Tanah",
        "uraian": "Sewa tanah kas desa untuk pertanian",
        "jenis": "pendapatan",
        "debit": 5000000,
        "kredit": 0,
        "saldo": 455000000,
        "file_bukti": "https://cdn.sikades.id/bukti/001.pdf"
      },
      {
        "id": 1235,
        "tanggal": "2024-12-21",
        "nomor_bukti": "BKU/002/XII/2024",
        "kode_rekening": "5.1.2.03",
        "nama_rekening": "Belanja ATK",
        "uraian": "Pembelian kertas A4 dan tinta printer",
        "jenis": "belanja",
        "debit": 0,
        "kredit": 1200000,
        "saldo": 453800000,
        "file_bukti": "https://cdn.sikades.id/bukti/002.pdf"
      }
    ],
    "meta": {
      "current_page": 1,
      "per_page": 50,
      "total": 523,
      "total_pages": 11,
      "has_next": true,
      "has_prev": false
    }
  }
}
```

#### 3.1.4 Get SPP List

**Endpoint:** `GET /dashboard/desa/{kode_desa}/spp`

**Query Parameters:**
- `status` (enum: `draft`, `submitted`, `approved`, `rejected`)
- `page`, `limit`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "items": [
      {
        "id": 456,
        "nomor_spp": "SPP/045/XII/20 24",
        "tanggal": "2024-12-15",
        "jenis": "LS",
        "keperluan": "Belanja honorarium kader posyandu",
        "nilai": 12000000,
        "status": "pending",
        "created_by": "Bendahara Desa",
        "created_at": "2024-12-15T10:00:00Z",
        "approved_at": null,
        "approved_by": null,
        "files": [
          {
            "name": "SK Kader.pdf",
            "url": "https://cdn.sikades.id/spp/sk_kader.pdf",
            "size": 1024000
          }
        ]
      }
    ],
    "meta": {
      "current_page": 1,
      "per_page": 50,
      "total": 3,
      "total_pages": 1
    }
  }
}
```

#### 3.1.5 Get Proyek List

**Endpoint:** `GET /dashboard/desa/{kode_desa}/proyek`

**Query Parameters:**
- `status` (enum: `belum_mulai`, `berjalan`, `selesai`, `mangkrak`)
- `bidang` (filter by bidang)

**Response (200):**
```json
{
  "success": true,
  "data": {
    "items": [
      {
        "id": 789,
        "nama": "Pembangunan Jalan Desa RT 03",
        "bidang": "Infrastruktur",
        "lokasi": "Dusun 1 RT 03 RW 01",
        "lat": -6.816514,
        "lng": 107.618034,
        "anggaran": 85000000,
        "realisasi": 42500000,
        "realisasi_persen": 50.0,
        "progress_persen": 55.0,
        "status": "berjalan",
        "tanggal_mulai": "2024-08-01",
        "tanggal_selesai_rencana": "2024-12-31",
        "tanggal_selesai_aktual": null,
        "tpk": "Pak Asep",
        "foto_progress": [
          {
            "tanggal": "2024-10-15",
            "url": "https://cdn.sikades.id/proyek/789/progress1.jpg",
            "caption": "Progress 30%"
          },
          {
            "tanggal": "2024-11-20",
            "url": "https://cdn.sikades.id/proyek/789/progress2.jpg",
            "caption": "Progress 55%"
          }
        ]
      }
    ],
    "meta": {
      "current_page": 1,
      "per_page": 50,
      "total": 12,
      "total_pages": 1
    }
  }
}
```

---

### 3.2 Dashboard Kecamatan

#### 3.2.1 Get Dashboard Overview

**Endpoint:** `GET /dashboard/kecamatan/{kode_kecamatan}`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "kode_kecamatan": "321601",
    "nama_kecamatan": "Lembang",
    "tahun": 2024,
    "jumlah_desa": 15,
    "updated_at": "2024-12-26T08:00:00Z",
    "agregat": {
      "total_anggaran": 37500000000,
      "total_realisasi": 29250000000,
      "avg_realisasi_persen": 78.0
    },
    "distribution": {
      "hijau": 4,
      "kuning": 8,
      "merah": 3
    },
    "top_performers": [
      {
        "rank": 1,
        "kode_desa": "3216012001",
        "nama_desa": "Lembang",
        "realisasi_persen": 95.2,
        "status": "green"
      },
      {
        "rank": 2,
        "kode_desa": "3216012002",
        "nama_desa": "Cihideung",
        "realisasi_persen": 92.1,
        "status": "green"
      }
    ],
    "need_assistance": [
      {
        "rank": 15,
        "kode_desa": "3216012015",
        "nama_desa": "Sariwangi",
        "realisasi_persen": 35.4,
        "status": "red"
      }
    ],
    "alerts": [
      {
        "id": 1,
        "priority": "urgent",
        "type": "realisasi_rendah",
        "message": "Desa Sariwangi realisasi < 50% di Q4",
        "kode_desa": "3216012015",
        "nama_desa": "Sariwangi",
        "created_at": "2024-12-25T10:00:00Z"
      }
    ]
  }
}
```

#### 3.2.2 Get Desa Ranking

**Endpoint:** `GET /dashboard/kecamatan/{kode_kecamatan}/ranking`

**Query Parameters:**
- `indicator` (default: `all`, options: `realisasi`, `kelengkapan_laporan`, `kecepatan_input`, `pelayanan`, `partisipasi`)
- `sort` (default: `desc`)

**Response (200):**
```json
{
  "success": true,
  "data": {
    "rankings": [
      {
        "rank": 1,
        "kode_desa": "3216012001",
        "nama_desa": "Lembang",
        "score_total": 88.5,
        "indicators": {
          "realisasi": 95.2,
          "kelengkapan_laporan": 100.0,
          "kecepatan_input": 85.0,
          "pelayanan": 90.0,
          "partisipasi": 72.0
        },
        "status": "green"
      },
      {
        "rank": 2,
        "kode_desa": "3216012002",
        "nama_desa": "Cihideung",
        "score_total": 85.2,
        "indicators": {
          "realisasi": 92.1,
          "kelengkapan_laporan": 95.0,
          "kecepatan_input": 80.0,
          "pelayanan": 88.0,
          "partisipasi": 70.0
        },
        "status": "green"
      }
      // ... 13 desa lagi
    ],
    "weights": {
      "realisasi": 0.30,
      "kelengkapan_laporan": 0.25,
      "kecepatan_input": 0.20,
      "pelayanan": 0.15,
      "partisipasi": 0.10
    }
  }
}
```

#### 3.2.3 Get Comparison Data

**Endpoint:** `GET /dashboard/kecamatan/{kode_kecamatan}/comparison`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "comparison_matrix": [
      {
        "kode_desa": "3216012001",
        "nama_desa": "Lembang",
        "realisasi": 95.2,
        "stunting": 4.4,
        "proyek_selesai": 83.3,
        "pelayanan_skor": 4.7
      }
      // ... 14 desa lagi
    ],
    "chart_data": {
      "labels": ["Lembang", "Cihideung", ...],
      "datasets": [
        {
          "label": "Realisasi (%)",
          "data": [95.2, 92.1, ...]
        }
      ]
    }
  }
}
```

---

### 3.3 Dashboard Kabupaten

#### 3.3.1 Get Dashboard Overview

**Endpoint:** `GET /dashboard/kabupaten/{kode_kabupaten}`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "kode_kabupaten": "3216",
    "nama_kabupaten": "Bandung Barat",
    "tahun": 2024,
    "jumlah_kecamatan": 16,
    "jumlah_desa": 165,
    "updated_at": "2024-12-26T08:00:00Z",
    "agregat": {
      "total_alokasi_add": 825000000000,
      "total_tersalurkan": 742500000000,
      "tersalurkan_persen": 90.0,
      "total_realisasi": 577500000000,
      "realisasi_persen": 70.0
    },
    "status_desa": {
      "hijau": 98,
      "hijau_persen": 59.4,
      "kuning": 52,
      "kuning_persen": 31.5,
      "merah": 15,
      "merah_persen": 9.1
    },
    "kelengkapan_laporan": {
      "lpj_semester_1": {
        "count": 162,
        "persen": 98.2
      },
      "tutup_buku_2023": {
        "count": 165,
        "persen": 100.0
      },
      "spj_apbdes": {
        "count": 158,
        "persen": 95.8
      }
    },
    "top_kecamatan": [
      {
        "rank": 1,
        "kode_kecamatan": "321601",
        "nama_kecamatan": "Lembang",
        "realisasi_persen": 85.2
      }
    ],
    "bottom_kecamatan": [
      {
        "rank": 16,
        "kode_kecamatan": "321616",
        "nama_kecamatan": "Saguling",
        "realisasi_persen": 52.1
      }
    ]
  }
}
```

#### 3.3.2 Get Sektor Analysis

**Endpoint:** `GET /dashboard/kabupaten/{kode_kabupaten}/sektor`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "by_sektor": [
      {
        "sektor": "Infrastruktur",
        "jumlah_proyek": 412,
        "proyek_persen": 45.0,
        "total_anggaran": 285000000000,
        "total_realisasi": 198000000000,
        "realisasi_persen": 69.5
      },
      {
        "sektor": "Ekonomi",
        "jumlah_proyek": 286,
        "proyek_persen": 31.2,
        "total_anggaran": 142000000000,
        "total_realisasi": 95000000000,
        "realisasi_persen": 66.9
      }
    ]
  }
}
```

---

### 3.4 Dashboard Provinsi

#### 3.4.1 Get Provincial Overview

**Endpoint:** `GET /dashboard/provinsi/{kode_provinsi}`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "kode_provinsi": "32",
    "nama_provinsi": "Jawa Barat",
    "tahun": 2024,
    "jumlah_kabupaten": 27,
    "jumlah_kecamatan": 626,
    "jumlah_desa": 5962,
    "updated_at": "2024-12-26T08:00:00Z",
    "agregat": {
      "total_alokasi_add": 29800000000000,
      "total_tersalurkan": 27118000000000,
      "tersalurkan_persen": 91.0,
      "total_realisasi": 20562000000000,
      "realisasi_persen": 69.0
    },
    "performance_kabupaten": {
      "bintang_5": 12,
      "bintang_4": 10,
      "bintang_3": 4,
      "below_3": 1
    },
    "kelengkapan_laporan_prov": {
      "lpj_semester_1_persen": 98.5,
      "tutup_buku_2023_persen": 99.8,
      "sipd_integration_persen": 92.3
    },
    "ranking_nasional": 2,
    "total_provinsi": 34
  }
}
```

#### 3.4.2 Get National Comparison

**Endpoint:** `GET /dashboard/provinsi/{kode_provinsi}/benchmarking`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "your_rank": 2,
    "your_score": 89.5,
    "top_3": [
      {
        "rank": 1,
        "kode_provinsi": "35",
        "nama_provinsi": "Jawa Timur",
        "score": 91.2
      },
      {
        "rank": 2,
        "kode_provinsi": "32",
        "nama_provinsi": "Jawa Barat",
        "score": 89.5
      },
      {
        "rank": 3,
        "kode_provinsi": "33",
        "nama_provinsi": "Jawa Tengah",
        "score": 87.8
      }
    ],
    "gap_to_first": -1.7,
    "national_avg": 72.5
  }
}
```

---

### 3.5 Common Endpoints

#### 3.5.1 Get User Profile

**Endpoint:** `GET /profile`

**Headers:**
```
Authorization: Bearer {access_token}
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "username": "admin_desa",
    "email": "admin@desalembang.id",
    "phone": "+6281234567890",
    "role": "kepala_desa",
    "level": "desa",
    "kode_desa": "3216012001",
    "nama_desa": "Lembang",
    "photo_url": "https://cdn.sikades.id/avatars/001.jpg",
    "preferences": {
      "theme": "light",
      "language": "id",
      "notifications": {
        "push": true,
        "email": false,
        "sms": false
      }
    },
    "created_at": "2024-01-15T10:30:00Z",
    "last_login_at": "2024-12-26T08:00:00Z"
  }
}
```

#### 3.5.2 Update User Profile

**Endpoint:** `PUT /profile`

**Request:**
```json
{
  "email": "newemail@desalembang.id",
  "phone": "+6281234567890",
  "preferences": {
    "theme": "dark",
    "notifications": {
      "push": true,
      "email": true
    }
  }
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Profile updated",
  "data": {
    // Updated user object
  }
}
```

#### 3.5.3 Upload Avatar

**Endpoint:** `POST /profile/avatar`

**Headers:**
```
Authorization: Bearer {access_token}
Content-Type: multipart/form-data
```

**Request (FormData):**
```
file: [binary image file]
```

**Response (200):**
```json
{
  "success": true,
  "message": "Avatar uploaded",
  "data": {
    "photo_url": "https://cdn.sikades.id/avatars/001_updated.jpg"
  }
}
```

#### 3.5.4 Get Notifications

**Endpoint:** `GET /notifications`

**Query Parameters:**
- `page`, `limit`
- `read` (boolean): Filter read/unread

**Response (200):**
```json
{
  "success": true,
  "data": {
    "items": [
      {
        "id": 1,
        "type": "spp_approval",
        "title": "SPP Baru Menunggu Approval",
        "message": "SPP/045/XII/2024 senilai Rp 12.000.000 menunggu approval",
        "priority": "high",
        "read": false,
        "data": {
          "spp_id": 456,
          "spp_nomor": "SPP/045/XII/2024"
        },
        "created_at": "2024-12-26T07:30:00Z"
      }
    ],
    "meta": {
      "unread_count": 5,
      "current_page": 1,
      "total": 23
    }
  }
}
```

#### 3.5.5 Mark Notification as Read

**Endpoint:** `PUT /notifications/{id}/read`

**Response (200):**
```json
{
  "success": true,
  "message": "Notification marked as read"
}
```

---

## 4. DATA MODELS

### 4.1 Common Response Structure

**Success Response:**
```typescript
interface ApiResponse<T> {
  success: true;
  message?: string;
  data: T;
  meta?: PaginationMeta;
}
```

**Error Response:**
```typescript
interface ApiErrorResponse {
  success: false;
  message: string;
  errors?: {
    code: string;
    details?: string;
    validation?: Record<string, string[]>;
  };
}
```

**Pagination Meta:**
```typescript
interface PaginationMeta {
  current_page: number;
  per_page: number;
  total: number;
  total_pages: number;
  has_next: boolean;
  has_prev: boolean;
}
```

### 4.2 Entity Models

**User:**
```typescript
interface User {
  id: number;
  username: string;
  email: string;
  phone?: string;
  role: 'kepala_desa' | 'camat' | 'kabag_bpkad' | 'gubernur';
  level: 'desa' | 'kecamatan' | 'kabupaten' | 'provinsi';
  kode_desa?: string;
  nama_desa?: string;
  kode_kecamatan?: string;
  nama_kecamatan?: string;
  kode_kabupaten?: string;
  nama_kabupaten?: string;
  kode_provinsi?: string;
  nama_provinsi?: string;
  photo_url?: string;
  preferences?: UserPreferences;
  created_at: string; // ISO 8601
  last_login_at: string;
}
```

**Dashboard Desa:**
```typescript
interface DashboardDesa {
  kode_desa: string;
  nama_desa: string;
  tahun: number;
  updated_at: string;
  keuangan: KeuanganStats;
  demografi: DemografiStats;
  pembangunan: PembangunanStats;
  kesehatan: KesehatanStats;
  pelayanan: PelayananStats;
}
```

---

## 5. ERROR HANDLING

### 5.1 HTTP Status Codes

| Code | Meaning | When Used |
|------|---------|-----------|
| 200 | OK | Successful GET, PUT |
| 201 | Created | Successful POST |
| 204 | No Content | Successful DELETE |
| 400 | Bad Request | Invalid input, validation error |
| 401 | Unauthorized | Missing or invalid token |
| 403 | Forbidden | Valid token but no permission |
| 404 | Not Found | Resource doesn't exist |
| 409 | Conflict | Duplicate resource |
| 422 | Unprocessable Entity | Validation failed |
| 429 | Too Many Requests | Rate limit exceeded |
| 500 | Internal Server Error | Server-side error |
| 503 | Service Unavailable | Maintenance mode |

### 5.2 Error Codes

| Code | Description |
|------|-------------|
| `INVALID_CREDENTIALS` | Username or password wrong |
| `TOKEN_EXPIRED` | JWT expired, refresh needed |
| `INSUFFICIENT_PERMISSION` | User doesn't have permission |
| `RESOURCE_NOT_FOUND` | Requested resource ID not found |
| `VALIDATION_ERROR` | Input validation failed |
| `RATE_LIMIT_EXCEEDED` | Too many requests |
| `MAINTENANCE_MODE` | System under maintenance |

### 5.3 Error Response Examples

**Validation Error (422):**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "code": "VALIDATION_ERROR",
    "validation": {
      "email": ["Email format is invalid"],
      "password": ["Password must be at least 8 characters"]
    }
  }
}
```

**Permission Error (403):**
```json
{
  "success": false,
  "message": "Access denied",
  "errors": {
    "code": "INSUFFICIENT_PERMISSION",
    "details": "You don't have permission to access kabupaten-level data"
  }
}
```

**Rate Limit (429):**
```json
{
  "success": false,
  "message": "Rate limit exceeded",
  "errors": {
    "code": "RATE_LIMIT_EXCEEDED",
    "details": "You can retry after 3600 seconds"
  },
  "retry_after": 3600
}
```

---

## 6. RATE LIMITING

### 6.1 Limits

| User Level | Requests per Hour | Burst |
|------------|-------------------|-------|
| Desa | 1,000 | 50 |
| Kecamatan | 5,000 | 100 |
| Kabupaten | 10,000 | 200 |
| Provinsi | 20,000 | 500 |

### 6.2 Headers

Response includes rate limit headers:
```
X-RateLimit-Limit: 1000
X-RateLimit-Remaining: 987
X-RateLimit-Reset: 1640534400
```

### 6.3 Implementation (NestJS)

```typescript
// rate-limit.guard.ts
@Injectable()
export class RateLimitGuard implements CanActivate {
  constructor(private redis: RedisService) {}
  
  async canActivate(context: ExecutionContext): Promise<boolean> {
    const request = context.switchToHttp().getRequest();
    const user = request.user;
    const key = `rate_limit:${user.id}`;
    
    const current = await this.redis.incr(key);
    if (current === 1) {
      await this.redis.expire(key, 3600); // 1 hour
    }
    
    const limit = this.getLimit(user.level);
    if (current > limit) {
      throw new HttpException('Rate limit exceeded', HttpStatus.TOO_MANY_REQUESTS);
    }
    
    return true;
  }
}
```

---

## 7. PAGINATION

### 7.1 Query Parameters

- `page`: Page number starting from 1 (default: 1)
- `limit`: Items per page (default: 50, max: 500)
- `sort`: Field to sort by (prefix with `-` for desc)

**Example:**
```
GET /dashboard/desa/3216012001/bku?page=2&limit=100&sort=-tanggal
```

### 7.2 Response Format

```json
{
  "data": {
    "items": [...],
    "meta": {
      "current_page": 2,
      "per_page": 100,
      "total": 523,
      "total_pages": 6,
      "has_next": true,
      "has_prev": true
    }
  }
}
```

---

## 8. FILTERING & SORTING

### 8.1 Filter Operators

Use query parameters:

| Operator | Example | Description |
|----------|---------|-------------|
| `eq` | `?status=eq:approved` | Equals |
| `ne` | `?status=ne:draft` | Not equals |
| `gt` | `?amount=gt:1000000` | Greater than |
| `gte` | `?amount=gte:1000000` | Greater than or equal |
| `lt` | `?amount=lt:5000000` | Less than |
| `lte` | `?amount=lte:5000000` | Less than or equal |
| `in` | `?status=in:approved,pending` | In list |
| `like` | `?uraian=like:honorarium` | Contains (case-insensitive) |
| `between` | `?tanggal=between:2024-01-01,2024-12-31` | Between range |

### 8.2 Complex Filtering

Multiple filters with AND logic:
```
GET /bku?jenis=eq:belanja&amount=gt:1000000&tanggal=between:2024-01-01,2024-12-31
```

### 8.3 Sorting

Multiple sort fields (comma-separated):
```
GET /bku?sort=-tanggal,amount
```
(Sort by tanggal DESC, then amount ASC)

---

## 9. WEBHOOKS

### 9.1 Subscribe to Webhooks

**Endpoint:** `POST /webhooks/subscribe`

**Request:**
```json
{
  "url": "https://your-app.com/webhook/sikades",
  "events": ["spp.approved", "project.completed", "alert.triggered"],
  "secret": "your_webhook_secret_key"
}
```

**Response (201):**
```json
{
  "success": true,
  "data": {
    "id": "webhook_123",
    "url": "https://your-app.com/webhook/sikades",
    "events": ["spp.approved", "project.completed", "alert.triggered"],
    "created_at": "2024-12-26T08:00:00Z"
  }
}
```

### 9.2 Webhook Payload

When event occurs, POST request sent to your URL:

```json
{
  "id": "evt_abc123",
  "event": "spp.approved",
  "timestamp": "2024-12-26T08:30:00Z",
  "data": {
    "spp_id": 456,
    "nomor_spp": "SPP/045/XII/2024",
    "nilai": 12000000,
    "approved_by": "Kepala Desa",
    "approved_at": "2024-12-26T08:30:00Z"
  }
}
```

**Headers sent:**
```
X-Sikades-Event: spp.approved
X-Sikades-Signature: sha256=abc123...
Content-Type: application/json
```

### 9.3 Verify Webhook Signature

```javascript
const crypto = require('crypto');

function verifyWebhookSignature(payload, signature, secret) {
  const hash = crypto
    .createHmac('sha256', secret)
    .update(JSON.stringify(payload))
    .digest('hex');
  
  return `sha256=${hash}` === signature;
}
```

---

## 10. SDK & LIBRARIES

### 10.1 Official SDKs

**Dart (Flutter):**
```dart
// pubspec.yaml
dependencies:
  sikades_api_client: ^1.0.0

// Usage
import 'package:sikades_api_client/sikades_api_client.dart';

final client = SikadesApiClient(
  baseUrl: 'https://api.sikades.id/v1',
  accessToken: 'your_jwt_token',
);

// Get dashboard
final dashboard = await client.dashboardDesa.get('3216012001');
print(dashboard.keuangan.totalAnggaran);

// Get BKU
final bku = await client.dashboardDesa.getBku(
  kodeDesa: '3216012001',
  page: 1,
  limit: 50,
);
```

**TypeScript (Node.js):**
```typescript
// npm install @sikades/api-client

import { SikadesApiClient } from '@sikades/api-client';

const client = new SikadesApiClient({
  baseUrl: 'https://api.sikades.id/v1',
  accessToken: 'your_jwt_token',
});

// Get dashboard
const dashboard = await client.dashboardDesa.get('3216012001');
console.log(dashboard.keuangan.totalAnggaran);
```

### 10.2 Code Examples

**cURL - Get Dashboard:**
```bash
curl -X GET https://api.sikades.id/v1/dashboard/desa/3216012001 \
  -H "Authorization: Bearer your_jwt_token" \
  -H "Accept: application/json"
```

**Python (requests):**
```python
import requests

headers = {
    'Authorization': 'Bearer your_jwt_token',
    'Accept': 'application/json',
}

response = requests.get(
    'https://api.sikades.id/v1/dashboard/desa/3216012001',
    headers=headers
)

data = response.json()
print(data['data']['keuangan']['total_anggaran'])
```

---

## APPENDIX: OpenAPI Specification

Full OpenAPI 3.0 spec available at:
```
https://api.sikades.id/v1/swagger.json
```

Interactive API docs:
```
https://api.sikades.id/v1/docs
```

---

**Document Version:** 1.0  
**Last Updated:** 26 Desember 2024  
**Author:** SIKADES Development Team  
**Contact:** api@sikades.id  
**Support:** https://docs.sikades.id

---

**Next: NestJS Implementation Guide** (separate document)
