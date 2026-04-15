# N-Tier Architecture Implementation - Dashboard Admin API

## Ringkasan Struktur

Saya telah membuat **Dashboard Admin API** dengan **Layered Architecture (N-Tier)** yang terdiri dari 4 layer utama:

```
┌──────────────────────────────────────────────────────┐
│            PRESENTATION LAYER (Handlers)             │
│  - admin_handler.go (Guru & Kelas)                  │
│  - siswa_tagihan_handler.go (Siswa, Tagihan, etc)  │
│  Tugas: HTTP request/response handling              │
└──────────────────────────────────────────────────────┘
                         ↓ ↑
┌──────────────────────────────────────────────────────┐
│           BUSINESS LOGIC LAYER (Services)            │
│  - admin_service.go (Dashboard, Guru, Kelas)        │
│  - siswa_tagihan_service.go (Siswa, Tagihan, Pby)  │
│  Tugas: Validasi & business logic                   │
└──────────────────────────────────────────────────────┘
                         ↓ ↑
┌──────────────────────────────────────────────────────┐
│          DATA ACCESS LAYER (Repository)              │
│  - dashboard.go (Dashboard & Guru queries)          │
│  - kelas_siswa.go (Kelas & Siswa queries)          │
│  - tagihan_pembayaran.go (Tagihan & Pembayaran)    │
│  Tugas: Database operations                         │
└──────────────────────────────────────────────────────┘
                         ↓ ↑
┌──────────────────────────────────────────────────────┐
│              DATABASE LAYER (MySQL)                  │
│  Tugas: Data storage & persistence                  │
└──────────────────────────────────────────────────────┘
```

---

## 1. PRESENTATION LAYER (Handlers)

### File: `handlers/admin_handler.go`
Menangani HTTP requests untuk Dashboard, Guru, dan Kelas.

**Endpoints:**
- Dashboard: GET metrics, GET statistics
- Guru: GET all, GET by ID, CREATE, UPDATE, DELETE
- Kelas: GET all, GET by ID, CREATE, DELETE

**Contoh Handler:**
```go
func GetAllGuru(c *gin.Context) {
    gurus, err := services.GetAllGurus(config.DB)
    if err != nil {
        c.JSON(http.StatusInternalServerError, ...)
        return
    }
    c.JSON(http.StatusOK, models.ApiResponse{
        Success: true,
        Data:    gurus,
    })
}
```

### File: `handlers/siswa_tagihan_handler.go`
Menangani HTTP requests untuk Siswa, Tagihan, dan Pembayaran.

**Endpoints:**
- Siswa: GET all, GET by ID, CREATE, DELETE
- Tagihan: GET all, GET by ID, CREATE, DELETE
- Pembayaran: GET all, GET by ID, UPDATE status

---

## 2. BUSINESS LOGIC LAYER (Services)

### File: `services/admin_service.go`

**Fungsi:**
```go
// Dashboard Services
GetDashboardOverview()     // Mengambil metrics
GetDashboardStatistics()   // Mengambil statistik

// Guru Services
GetAllGurus()              // Ambil semua guru
GetGuruDetail()            // Ambil detail guru
CreateNewGuru()            // Validasi & create guru
UpdateGuruData()           // Validasi & update guru
RemoveGuru()               // Validasi & delete guru

// Kelas Services
GetAllKelas()              // Ambil semua kelas
GetKelasDetail()           // Ambil detail kelas
CreateNewKelas()           // Validasi & create kelas
RemoveKelas()              // Validasi & delete kelas
```

**Contoh Service:**
```go
func CreateNewGuru(db *sql.DB, guruReq *models.CreateGuruRequest) (int64, error) {
    // 1. VALIDASI INPUT
    if guruReq.NamaGuru == "" {
        return 0, fmt.Errorf("nama guru tidak boleh kosong")
    }
    
    // 2. CALL REPOSITORY
    id, err := repository.CreateGuru(db, guruReq)
    if err != nil {
        return 0, err
    }
    
    return id, nil
}
```

### File: `services/siswa_tagihan_service.go`

**Fungsi:**
```go
// Siswa Services
GetAllSiswa()              // Ambil semua siswa
GetSiswaDetail()           // Ambil detail siswa
GetSiswaByKelas()          // Ambil siswa per kelas
CreateNewSiswa()           // Validasi & create siswa
RemoveSiswa()              // Validasi & delete siswa

// Tagihan Services
GetAllTagihan()            // Ambil semua tagihan
GetTagihanDetail()         // Ambil detail tagihan
GetTagihanBySiswa()        // Ambil tagihan per siswa
CreateNewTagihan()         // Validasi & create tagihan
RemoveTagihan()            // Validasi & delete tagihan

// Pembayaran Services
GetAllPembayaran()         // Ambil semua pembayaran
GetPembayaranDetail()      // Ambil detail pembayaran
GetPembayaranByTagihan()   // Ambil pembayaran per tagihan
UpdatePembayaranStatusService() // Validasi & update status
```

---

## 3. DATA ACCESS LAYER (Repository)

### File: `repository/dashboard.go`

**Query Functions:**
```sql
-- Get Dashboard Metrics
SELECT COUNT(*) FROM siswa, guru, kelas, tagihan
AND calculate pemasukan, hutang, dll.

-- Get All Guru dengan Join
SELECT g.*, COUNT(k.id_kelas), COUNT(s.nomor_induk_siswa)
FROM guru g
LEFT JOIN kelas k ON g.id_guru = k.id_guru
LEFT JOIN siswa s ON k.id_kelas = s.id_kelas
GROUP BY g.id_guru

-- Create Guru
INSERT INTO guru (nama_guru, no_hp, email, created_at, updated_at)
VALUES (?, ?, ?, NOW(), NOW())
```

### File: `repository/kelas_siswa.go`

**Query Functions:**
```sql
-- Get All Kelas dengan Detail
SELECT k.*, g.nama_guru, COUNT(DISTINCT s.nomor_induk_siswa)
FROM kelas k
LEFT JOIN guru g ON k.id_guru = g.id_guru
LEFT JOIN siswa s ON k.id_kelas = s.id_kelas
GROUP BY k.id_kelas

-- Get Siswa Detail dengan Tagihan & Pembayaran
SELECT s.*, k.nama_kelas, 
       SUM(t.jumlah_tagihan), 
       SUM(p.jumlah_bayar WHERE status = 'diterima')
FROM siswa s
LEFT JOIN kelas k ON s.id_kelas = k.id_kelas
LEFT JOIN tagihan t ON s.nomor_induk_siswa = t.nomor_induk_siswa
LEFT JOIN pembayaran p ON t.id_tagihan = p.id_tagihan
GROUP BY s.nomor_induk_siswa
```

### File: `repository/tagihan_pembayaran.go`

**Query Functions:**
```sql
-- Get All Tagihan dengan Pembayaran
SELECT t.*, s.nama_anak,
       SUM(p.jumlah_bayar WHERE status = 'diterima'),
       (t.jumlah_tagihan - SUM(p.jumlah_bayar))
FROM tagihan t
LEFT JOIN siswa s ON t.nomor_induk_siswa = s.nomor_induk_siswa
LEFT JOIN pembayaran p ON t.id_tagihan = p.id_tagihan
GROUP BY t.id_tagihan

-- Update Pembayaran Status
UPDATE pembayaran SET status_bayar = ?, updated_at = NOW()
WHERE id_pembayaran = ?
```

---

## 4. MODEL/ENTITY LAYER

### File: `models/admin.go`

**Entity Models:**
```go
type Admin struct {
    IDAdmin   int
    Username  string
    Email     string
    Role      string
}

type DashboardMetrics struct {
    TotalSiswa        int64
    TotalGuru         int64
    TotalTagihan      int64
    TotalPemasukanBulan float64
    // dll...
}

type GuruDetail struct {
    IDGuru     int
    NamaGuru   string
    TotalKelas int
    TotalSiswa int
}

type SiswaDetail struct {
    NomorIndukSiswa int
    NamaAnak        string
    Kelas           string
    TotalTagihan    float64
    TotalBayar      float64
    StatusPembayaran string
}
```

**Request/Response DTOs:**
```go
type CreateGuruRequest struct {
    NamaGuru string `json:"nama_guru" binding:"required"`
    NoHP     string `json:"no_hp" binding:"required"`
    Email    string `json:"email" binding:"required,email"`
}

type UpdatePembayaranRequest struct {
    IDPembayaran int    `json:"id_pembayaran" binding:"required"`
    StatusBayar  string `json:"status_bayar" binding:"required"`
}
```

---

## 5. ROUTING LAYER

### File: `routes/admin_routes.go`

```go
func SetupAdminRoutes(r *gin.Engine) {
    admin := r.Group("/api/admin")
    {
        // Dashboard Routes
        dashboard := admin.Group("/dashboard")
        dashboard.GET("/metrics", handlers.GetDashboardMetrics)
        dashboard.GET("/statistics", handlers.GetDashboardStatistics)
        
        // Guru Routes
        guru := admin.Group("/guru")
        guru.GET("", handlers.GetAllGuru)
        guru.POST("", handlers.CreateGuru)
        // ... dll
        
        // Kelas Routes
        kelas := admin.Group("/kelas")
        // ... dll
        
        // Siswa Routes
        siswa := admin.Group("/siswa")
        // ... dll
        
        // Tagihan Routes
        tagihan := admin.Group("/tagihan")
        // ... dll
        
        // Pembayaran Routes
        pembayaran := admin.Group("/pembayaran")
        // ... dll
    }
}
```

---

## Keuntungan N-Tier Architecture

✅ **Separation of Concerns**: Setiap layer punya tanggung jawab spesifik  
✅ **Reusability**: Service dapat digunakan oleh multiple handlers  
✅ **Testability**: Mudah untuk unit testing setiap layer  
✅ **Maintainability**: Mudah untuk mengupdate business logic  
✅ **Scalability**: Dapat dengan mudah menambah fitur baru  
✅ **Flexibility**: Dapat mengganti database tanpa mengubah handler  

---

## Request Flow Example

```
HTTP Request (POST /api/admin/guru)
    ↓
Handler (admin_handler.go)
├─ Parse request body
├─ Validate input
└─ Call service
    ↓
Service (admin_service.go)
├─ Business logic validation
├─ Check if guru exists
└─ Call repository
    ↓
Repository (dashboard.go)
├─ SQL Query: INSERT INTO guru
└─ Return last insert ID
    ↓
Service
└─ Return result
    ↓
Handler
└─ Return HTTP Response (201 Created)
    ↓
Client
```

---

## File Structure

```
backend/
├── main.go                          # Entry point (updated dengan routes)
├── API_DOCUMENTATION.md             # Dokumentasi API
├── ARCHITECTURE.md                  # File ini
├── config/
│   ├── database.go
│   └── config.go
├── handlers/                        # PRESENTATION LAYER
│   ├── admin_handler.go
│   └── siswa_tagihan_handler.go
├── services/                        # BUSINESS LOGIC LAYER
│   ├── admin_service.go
│   └── siswa_tagihan_service.go
├── repository/                      # DATA ACCESS LAYER
│   ├── dashboard.go
│   ├── kelas_siswa.go
│   └── tagihan_pembayaran.go
├── models/                          # ENTITY/MODEL LAYER
│   ├── models.go
│   └── admin.go
├── routes/                          # ROUTING LAYER
│   └── admin_routes.go
├── middleware/
├── migrations/
└── Dockerfile (optional)
```

---

## Summary Endpoints

**Total: 27 Endpoints**

| Module | GET | POST | PUT | DELETE | Total |
|--------|-----|------|-----|--------|-------|
| Dashboard | 2 | 0 | 0 | 0 | 2 |
| Guru | 2 | 1 | 1 | 1 | 5 |
| Kelas | 2 | 1 | 0 | 1 | 4 |
| Siswa | 3 | 1 | 0 | 1 | 5 |
| Tagihan | 3 | 1 | 0 | 1 | 5 |
| Pembayaran | 2 | 0 | 1 | 0 | 3 |
| **TOTAL** | **14** | **4** | **2** | **4** | **24** |

(Plus 1 endpoint dashboard = **25 endpoints**)

---

## Database Schema

Menggunakan existing database schema dari `database_setup.sql`:

**Tabel:**
- guru
- kelas
- siswa
- akun
- pengumuman
- perkembangan
- tagihan
- pembayaran

**Relationships:**
- guru → kelas → siswa
- siswa → tagihan → pembayaran
- guru → pengumuman
- guru → perkembangan

---

## Next Steps

1. ✅ Backend API dengan N-Tier Architecture - **DONE**
2. ⏳ Setup Flutter Dashboard UI
3. ⏳ Create admin login/authentication
4. ⏳ Create admin dashboard UI
5. ⏳ Integrate API calls from Flutter

---

**Last Updated:** April 15, 2026  
**Version:** 1.0.0
