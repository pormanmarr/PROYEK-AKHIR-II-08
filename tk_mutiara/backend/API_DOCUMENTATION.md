# Dashboard Admin API - Dokumentasi

## Arsitektur N-Tier (Layered Architecture)

```
┌─────────────────────────────────────────────────────────────┐
│                  Presentation Layer (Handlers)              │
│    - HTTP Request/Response Processing                       │
│    - Input Validation & Error Handling                      │
└─────────────────────────────────────────────────────────────┘
                           ↓↑
┌─────────────────────────────────────────────────────────────┐
│                   Business Logic Layer (Services)           │
│    - Business Rules & Validations                           │
│    - Data Processing & Transformations                      │
│    - Orchestration of Repository Calls                      │
└─────────────────────────────────────────────────────────────┘
                           ↓↑
┌─────────────────────────────────────────────────────────────┐
│                 Data Access Layer (Repository)              │
│    - Database Queries (SELECT, INSERT, UPDATE, DELETE)      │
│    - SQL Execution & Result Mapping                         │
└─────────────────────────────────────────────────────────────┘
                           ↓↑
┌─────────────────────────────────────────────────────────────┐
│                   Database Layer (MySQL)                    │
│    - Data Storage & Persistence                             │
│    - Indexes & Relationships                                │
└─────────────────────────────────────────────────────────────┘
```

## Base URL
```
http://localhost:8080/api/admin
```

## Struktur Folder Backend

```
backend/
├── config/
│   └── database configuration
├── handlers/
│   ├── admin_handler.go          # Handler untuk Guru & Kelas
│   └── siswa_tagihan_handler.go  # Handler untuk Siswa, Tagihan, Pembayaran
├── models/
│   ├── models.go                 # Model umum
│   └── admin.go                  # Admin-specific models & DTOs
├── repository/
│   ├── dashboard.go              # Dashboard & Guru queries
│   ├── kelas_siswa.go            # Kelas & Siswa queries
│   └── tagihan_pembayaran.go     # Tagihan & Pembayaran queries
├── routes/
│   └── admin_routes.go           # Route registration
├── services/
│   ├── admin_service.go          # Dashboard, Guru, Kelas business logic
│   └── siswa_tagihan_service.go  # Siswa, Tagihan, Pembayaran business logic
└── main.go                       # Entry point
```

---

## API ENDPOINTS

### 1. DASHBOARD ENDPOINTS

#### GET /api/admin/dashboard/metrics
Mengambil metrics overview dashboard

**Response:**
```json
{
  "success": true,
  "message": "Dashboard metrics berhasil diambil",
  "data": {
    "total_siswa": 150,
    "total_guru": 12,
    "total_kelas": 8,
    "total_tagihan": 450,
    "tagihan_terbayar": 320,
    "tagihan_belum_bayar": 130,
    "total_pemasukan_bulan": 5000000,
    "total_hutang_bulan": 2500000
  }
}
```

#### GET /api/admin/dashboard/statistics?limit=30
Mengambil statistik dashboard (default 30 hari terakhir)

**Query Parameters:**
- `limit` (optional, default: 30) - Jumlah hari

**Response:**
```json
{
  "success": true,
  "message": "Dashboard statistics berhasil diambil",
  "data": [
    {
      "date": "2026-04-15",
      "total_data": 5,
      "revenue": 250000,
      "status": "success"
    },
    {
      "date": "2026-04-14",
      "total_data": 3,
      "revenue": 150000,
      "status": "success"
    }
  ]
}
```

---

### 2. GURU ENDPOINTS

#### GET /api/admin/guru
Mengambil semua data guru

**Response:**
```json
{
  "success": true,
  "message": "Data guru berhasil diambil",
  "data": [
    {
      "id_guru": 1,
      "nama_guru": "Ibu Siti",
      "no_hp": "081234567890",
      "email": "siti@tkmutiara.com",
      "total_kelas": 2,
      "total_siswa": 45,
      "created_at": "2026-01-15"
    }
  ]
}
```

#### GET /api/admin/guru/:id
Mengambil detail guru berdasarkan ID

**Response:**
```json
{
  "success": true,
  "message": "Data guru berhasil diambil",
  "data": {
    "id_guru": 1,
    "nama_guru": "Ibu Siti",
    "no_hp": "081234567890",
    "email": "siti@tkmutiara.com",
    "total_kelas": 2,
    "total_siswa": 45,
    "created_at": "2026-01-15"
  }
}
```

#### POST /api/admin/guru
Membuat guru baru

**Request Body:**
```json
{
  "nama_guru": "Ibu Ahmad",
  "no_hp": "081234567891",
  "email": "ahmad@tkmutiara.com"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Guru berhasil dibuat",
  "data": {
    "id_guru": 2
  }
}
```

#### PUT /api/admin/guru/:id
Update data guru

**Request Body:**
```json
{
  "nama_guru": "Ibu Ahmad Updated",
  "no_hp": "081234567891",
  "email": "ahmad.updated@tkmutiara.com"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Guru berhasil diupdate"
}
```

#### DELETE /api/admin/guru/:id
Menghapus guru

**Response:**
```json
{
  "success": true,
  "message": "Guru berhasil dihapus"
}
```

---

### 3. KELAS ENDPOINTS

#### GET /api/admin/kelas
Mengambil semua kelas

**Response:**
```json
{
  "success": true,
  "message": "Data kelas berhasil diambil",
  "data": [
    {
      "id_kelas": 1,
      "nama_kelas": "Kelompok A",
      "nama_guru": "Ibu Siti",
      "total_siswa": 20,
      "created_at": "2026-01-20"
    }
  ]
}
```

#### GET /api/admin/kelas/:id
Mengambil detail kelas

**Response:**
```json
{
  "success": true,
  "message": "Data kelas berhasil diambil",
  "data": {
    "id_kelas": 1,
    "nama_kelas": "Kelompok A",
    "nama_guru": "Ibu Siti",
    "total_siswa": 20,
    "created_at": "2026-01-20"
  }
}
```

#### POST /api/admin/kelas
Membuat kelas baru

**Request Body:**
```json
{
  "id_guru": 1,
  "nama_kelas": "Kelompok B"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Kelas berhasil dibuat",
  "data": {
    "id_kelas": 2
  }
}
```

#### GET /api/admin/kelas/:id/siswa
Mengambil siswa dalam kelas tertentu

**Response:**
```json
{
  "success": true,
  "message": "Data siswa berhasil diambil",
  "data": [
    {
      "nomor_induk_siswa": 1,
      "nama_anak": "Bintang",
      "nama_orgtua": "Budi",
      "kelas": "Kelompok A",
      "jenis_kelamin": "L",
      "tgl_lahir": "2021-05-10",
      "alamat": "Jl. Merdeka No. 5",
      "total_tagihan": 500000,
      "total_bayar": 300000,
      "sisa_tagihan": 200000,
      "status_pembayaran": "Belum Lunas",
      "created_at": "2026-01-25"
    }
  ]
}
```

#### DELETE /api/admin/kelas/:id
Menghapus kelas

**Response:**
```json
{
  "success": true,
  "message": "Kelas berhasil dihapus"
}
```

---

### 4. SISWA ENDPOINTS

#### GET /api/admin/siswa
Mengambil semua siswa

**Response:**
```json
{
  "success": true,
  "message": "Data siswa berhasil diambil",
  "data": [
    {
      "nomor_induk_siswa": 1,
      "nama_anak": "Bintang",
      "nama_orgtua": "Budi",
      "kelas": "Kelompok A",
      "jenis_kelamin": "L",
      "tgl_lahir": "2021-05-10",
      "alamat": "Jl. Merdeka No. 5",
      "total_tagihan": 500000,
      "total_bayar": 300000,
      "sisa_tagihan": 200000,
      "status_pembayaran": "Belum Lunas",
      "created_at": "2026-01-25"
    }
  ]
}
```

#### GET /api/admin/siswa/:id
Mengambil detail siswa

**Response:**
```json
{
  "success": true,
  "message": "Data siswa berhasil diambil",
  "data": {
    "nomor_induk_siswa": 1,
    "nama_anak": "Bintang",
    "nama_orgtua": "Budi",
    "kelas": "Kelompok A",
    "jenis_kelamin": "L",
    "tgl_lahir": "2021-05-10",
    "alamat": "Jl. Merdeka No. 5",
    "total_tagihan": 500000,
    "total_bayar": 300000,
    "sisa_tagihan": 200000,
    "status_pembayaran": "Belum Lunas",
    "created_at": "2026-01-25"
  }
}
```

#### POST /api/admin/siswa
Membuat siswa baru

**Request Body:**
```json
{
  "id_kelas": 1,
  "nama_anak": "Bintang Indah",
  "nama_orgtua": "Budi Santoso",
  "tgl_lahir": "2021-05-10",
  "jenis_kelamin": "L",
  "alamat": "Jl. Merdeka No. 5"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Siswa berhasil dibuat",
  "data": {
    "nomor_induk_siswa": 1
  }
}
```

#### GET /api/admin/siswa/:id/tagihan
Mengambil tagihan siswa

**Response:**
```json
{
  "success": true,
  "message": "Data tagihan berhasil diambil",
  "data": [
    {
      "id_tagihan": 1,
      "nomor_induk_siswa": 1,
      "nama_anak": "Bintang",
      "jumlah_tagihan": 300000,
      "periode": "April 2026",
      "status": "belum_bayar",
      "total_bayar": 0,
      "sisa_bayar": 300000,
      "created_at": "2026-04-01"
    }
  ]
}
```

#### DELETE /api/admin/siswa/:id
Menghapus siswa

**Response:**
```json
{
  "success": true,
  "message": "Siswa berhasil dihapus"
}
```

---

### 5. TAGIHAN ENDPOINTS

#### GET /api/admin/tagihan
Mengambil semua tagihan

**Response:**
```json
{
  "success": true,
  "message": "Data tagihan berhasil diambil",
  "data": [
    {
      "id_tagihan": 1,
      "nomor_induk_siswa": 1,
      "nama_anak": "Bintang",
      "jumlah_tagihan": 300000,
      "periode": "April 2026",
      "status": "belum_bayar",
      "total_bayar": 100000,
      "sisa_bayar": 200000,
      "created_at": "2026-04-01"
    }
  ]
}
```

#### GET /api/admin/tagihan/:id
Mengambil detail tagihan

**Response:**
```json
{
  "success": true,
  "message": "Data tagihan berhasil diambil",
  "data": {
    "id_tagihan": 1,
    "nomor_induk_siswa": 1,
    "nama_anak": "Bintang",
    "jumlah_tagihan": 300000,
    "periode": "April 2026",
    "status": "belum_bayar",
    "total_bayar": 100000,
    "sisa_bayar": 200000,
    "created_at": "2026-04-01"
  }
}
```

#### POST /api/admin/tagihan
Membuat tagihan baru

**Request Body:**
```json
{
  "nomor_induk_siswa": 1,
  "jumlah_tagihan": 300000,
  "periode": "April 2026"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Tagihan berhasil dibuat",
  "data": {
    "id_tagihan": 1
  }
}
```

#### GET /api/admin/tagihan/:id/pembayaran
Mengambil pembayaran dari tagihan tertentu

**Response:**
```json
{
  "success": true,
  "message": "Data pembayaran berhasil diambil",
  "data": [
    {
      "id_pembayaran": 1,
      "id_tagihan": 1,
      "nama_anak": "Bintang",
      "jumlah_bayar": 100000,
      "tgl_pembayaran": "2026-04-10",
      "status_bayar": "diterima",
      "created_at": "2026-04-10"
    }
  ]
}
```

#### DELETE /api/admin/tagihan/:id
Menghapus tagihan

**Response:**
```json
{
  "success": true,
  "message": "Tagihan berhasil dihapus"
}
```

---

### 6. PEMBAYARAN ENDPOINTS

#### GET /api/admin/pembayaran
Mengambil semua pembayaran

**Response:**
```json
{
  "success": true,
  "message": "Data pembayaran berhasil diambil",
  "data": [
    {
      "id_pembayaran": 1,
      "id_tagihan": 1,
      "nama_anak": "Bintang",
      "jumlah_bayar": 100000,
      "tgl_pembayaran": "2026-04-10",
      "status_bayar": "diterima",
      "created_at": "2026-04-10"
    }
  ]
}
```

#### GET /api/admin/pembayaran/:id
Mengambil detail pembayaran

**Response:**
```json
{
  "success": true,
  "message": "Data pembayaran berhasil diambil",
  "data": {
    "id_pembayaran": 1,
    "id_tagihan": 1,
    "nama_anak": "Bintang",
    "jumlah_bayar": 100000,
    "tgl_pembayaran": "2026-04-10",
    "status_bayar": "diterima",
    "created_at": "2026-04-10"
  }
}
```

#### PUT /api/admin/pembayaran/:id/status
Update status pembayaran

**Request Body:**
```json
{
  "status_bayar": "diterima"
}
```

**Status Valid:**
- `menunggu` - Menunggu verifikasi
- `diterima` - Pembayaran diterima
- `ditolak` - Pembayaran ditolak

**Response:**
```json
{
  "success": true,
  "message": "Status pembayaran berhasil diupdate"
}
```

---

## Error Responses

### 400 Bad Request
```json
{
  "success": false,
  "error": "Invalid input data"
}
```

### 404 Not Found
```json
{
  "success": false,
  "error": "Resource not found"
}
```

### 500 Internal Server Error
```json
{
  "success": false,
  "error": "Internal server error"
}
```

---

## Contoh Testing dengan cURL

### Get Dashboard Metrics
```bash
curl -X GET http://localhost:8080/api/admin/dashboard/metrics
```

### Create Guru
```bash
curl -X POST http://localhost:8080/api/admin/guru \
  -H "Content-Type: application/json" \
  -d '{
    "nama_guru": "Ibu Siti",
    "no_hp": "081234567890",
    "email": "siti@tkmutiara.com"
  }'
```

### Get All Siswa
```bash
curl -X GET http://localhost:8080/api/admin/siswa
```

### Update Pembayaran Status
```bash
curl -X PUT http://localhost:8080/api/admin/pembayaran/1/status \
  -H "Content-Type: application/json" \
  -d '{
    "status_bayar": "diterima"
  }'
```

---

## Fitur Backend

✅ Dashboard Metrics & Statistics  
✅ CRUD Guru  
✅ CRUD Kelas  
✅ CRUD Siswa  
✅ CRUD Tagihan  
✅ Update Pembayaran Status  
✅ Database Queries Teroptimasi  
✅ Error Handling  
✅ Validasi Input  
✅ CORS Support  

---

## Next Steps

1. ✅ Backend API dengan N-Tier Architecture - DONE
2. ⏳ Dashboard Admin UI dengan Flutter
3. ⏳ Authentication & Authorization
4. ⏳ Integration Testing

---

**Created Date:** April 15, 2026  
**Last Updated:** April 15, 2026
