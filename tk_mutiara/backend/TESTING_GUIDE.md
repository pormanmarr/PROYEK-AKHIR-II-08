# API Testing Guide - Dashboard Admin API

## Metode Testing & Langkah-Langkah

### **1. QUICK START - cURL (TERCEPAT)**

**Pastikan backend sudah berjalan:**
```bash
# Terminal di folder backend
go run main.go
```

**Di terminal lain, jalankan test commands:**

#### A. Test Dashboard Metrics
```bash
curl -X GET http://localhost:8080/api/admin/dashboard/metrics | jq
```

Expected Response:
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

#### B. Test Create Guru
```bash
curl -X POST http://localhost:8080/api/admin/guru \
  -H "Content-Type: application/json" \
  -d '{
    "nama_guru": "Ibu Siti",
    "no_hp": "081234567890",
    "email": "siti@tkmutiara.com"
  }' | jq
```

Expected Response:
```json
{
  "success": true,
  "message": "Guru berhasil dibuat",
  "data": {
    "id_guru": 1
  }
}
```

#### C. Test Get All Guru
```bash
curl -X GET http://localhost:8080/api/admin/guru | jq
```

#### D. Test Error Handling (Empty Name)
```bash
curl -X POST http://localhost:8080/api/admin/guru \
  -H "Content-Type: application/json" \
  -d '{
    "nama_guru": "",
    "no_hp": "081234567890",
    "email": "siti@tkmutiara.com"
  }' | jq
```

Expected Response (Error):
```json
{
  "success": false,
  "error": "nama guru tidak boleh kosong"
}
```

---

### **2. REST CLIENT EXTENSION (GUI - Mudah)**

**Setup di VS Code:**

1. Install extension: **REST Client** (Huachao Mao)
   - Buka VS Code → Extensions → Cari "REST Client" → Install

2. Buka file: `backend/test_api.http`

3. Klik tombol **"Send Request"** di atas setiap endpoint

**Kelebihan:**
- ✅ Visual & mudah digunakan
- ✅ Bisa save history
- ✅ Beda warna untuk status code
- ✅ Auto-format response

**Contoh penggunaan:**
- Klik "Send Request" di atas `GET /dashboard/metrics`
- Response muncul di panel sebelah kanan
- Bisa langsung lihat status code, response time, dll

---

### **3. POSTMAN (Professional)**

**Download & Setup:**

1. Download dari [postman.com](https://www.postman.com/downloads/)

2. Create workspace baru: "TK Mutiara"

3. Create collection: "Admin Dashboard API"

4. Add requests untuk setiap endpoint:

**Example: Create Guru**
```
Method: POST
URL: http://localhost:8080/api/admin/guru
Headers: Content-Type: application/json
Body (JSON):
{
  "nama_guru": "Ibu Siti",
  "no_hp": "081234567890",
  "email": "siti@tkmutiara.com"
}
```

**Tips:**
- Gunakan environment variables untuk base URL
- Save responses untuk dokumentasi
- Gunakan tests untuk automated validation

---

### **4. UNIT TESTING dengan Go `testing` Package**

File sudah tersedia: `services/admin_service_test.go`

**Jalankan testing:**

```bash
# Dari folder backend
cd backend

# Run all tests
go test ./...

# Run dengan verbose output
go test ./... -v

# Run specific test
go test ./services -run TestCreateNewGuru

# Run dengan coverage report
go test ./... -cover
go test ./... -coverprofile=coverage.out
go tool cover -html=coverage.out
```

**Contoh output:**
```
ok      tk_mutiara_backend/services     0.123s  coverage: 35.2% of statements
ok      tk_mutiara_backend/handlers     0.456s  coverage: 28.7% of statements
```

---

## Checklist Testing Lengkap

### Dashboard Endpoints ✓
- [ ] GET `/dashboard/metrics` - Ambil metrics
- [ ] GET `/dashboard/statistics` - Ambil statistik

### Guru Endpoints ✓
- [ ] GET `/guru` - Ambil semua guru
- [ ] GET `/guru/:id` - Ambil guru by ID
- [ ] POST `/guru` - Create guru (valid)
- [ ] POST `/guru` - Create guru (invalid email)
- [ ] POST `/guru` - Create guru (empty name)
- [ ] PUT `/guru/:id` - Update guru
- [ ] DELETE `/guru/:id` - Delete guru

### Kelas Endpoints ✓
- [ ] GET `/kelas` - Ambil semua kelas
- [ ] GET `/kelas/:id` - Ambil kelas by ID
- [ ] GET `/kelas/:id/siswa` - Ambil siswa in kelas
- [ ] POST `/kelas` - Create kelas
- [ ] DELETE `/kelas/:id` - Delete kelas

### Siswa Endpoints ✓
- [ ] GET `/siswa` - Ambil semua siswa
- [ ] GET `/siswa/:id` - Ambil siswa by ID
- [ ] POST `/siswa` - Create siswa
- [ ] DELETE `/siswa/:id` - Delete siswa

### Tagihan Endpoints ✓
- [ ] GET `/tagihan` - Ambil semua tagihan
- [ ] GET `/tagihan/:id` - Ambil tagihan by ID
- [ ] GET `/siswa/:id/tagihan` - Ambil tagihan by siswa
- [ ] POST `/tagihan` - Create tagihan
- [ ] DELETE `/tagihan/:id` - Delete tagihan

### Pembayaran Endpoints ✓
- [ ] GET `/pembayaran` - Ambil semua pembayaran
- [ ] GET `/pembayaran/:id` - Ambil pembayaran by ID
- [ ] GET `/tagihan/:id/pembayaran` - Ambil pembayaran by tagihan
- [ ] PUT `/pembayaran/:id/status` - Update status (diterima)
- [ ] PUT `/pembayaran/:id/status` - Update status (ditolak)
- [ ] PUT `/pembayaran/:id/status` - Update status (menunggu)

---

## HTTP Status Codes Reference

| Code | Meaning | Example |
|------|---------|---------|
| 200 | OK | GET request berhasil |
| 201 | Created | POST berhasil create resource |
| 400 | Bad Request | Invalid input data |
| 404 | Not Found | Resource tidak ditemukan |
| 500 | Server Error | Error di backend |

---

## Common Issues & Solutions

### ❌ Issue: `Connection refused`
**Solusi:** Backend belum running, jalankan:
```bash
go run main.go
```

### ❌ Issue: `404 Not Found`
**Solusi:** 
- Check route path sudah benar
- Check endpoint registered di routes
- Verify base URL is correct

### ❌ Issue: `400 Bad Request`
**Solusi:**
- Check JSON format valid
- Check required fields ada
- Check data types sesuai

### ❌ Issue: `500 Internal Server Error`
**Solusi:**
- Check backend logs
- Verify database connection
- Check SQL queries

---

## Tips & Best Practices

✅ **Test secara bertahap:**
1. Test GET endpoints dulu (read-only)
2. Test POST endpoints (create)
3. Test PUT/DELETE (modify/delete)

✅ **Gunakan data valid:**
- Pastikan ID yang di-reference ada di database
- Gunakan format data sesuai schema

✅ **Check response carefully:**
- Baca error message
- Verify structure response
- Check data values

✅ **Test error scenarios:**
- Empty/invalid input
- Non-existent resources
- Duplicate data

---

## Recommended Testing Flow

```
1. START BACKEND
   └─ go run main.go

2. TEST DASHBOARD
   └─ GET /dashboard/metrics
   └─ Check response structure

3. TEST GURU CRUD
   ├─ GET /guru (should be empty or existing)
   ├─ POST /guru (create test guru)
   ├─ GET /guru/:id (verify created)
   ├─ PUT /guru/:id (update test guru)
   └─ DELETE /guru/:id (delete test guru)

4. TEST KELAS CRUD
   ├─ POST /kelas (need valid id_guru from step 3)
   ├─ GET /kelas
   ├─ GET /kelas/:id
   └─ DELETE /kelas/:id

5. TEST SISWA CRUD
   ├─ POST /siswa (need valid id_kelas from step 4)
   ├─ GET /siswa
   ├─ GET /siswa/:id
   └─ DELETE /siswa/:id

6. TEST TAGIHAN & PEMBAYARAN
   ├─ POST /tagihan (need valid nomor_induk_siswa)
   ├─ GET /tagihan
   └─ PUT /pembayaran/:id/status

7. VERIFY ALL RESPONSES
   └─ Check success status true/false
   └─ Check data structure valid
   └─ Check error messages clear
```

---

## Commands Summary

```bash
# START BACKEND
go run main.go

# RUN UNIT TESTS
go test ./...

# TEST WITH CURL
curl -X GET http://localhost:8080/api/admin/guru

# TEST WITH jq (pretty print)
curl -X GET http://localhost:8080/api/admin/guru | jq

# FORMAT JSON RESPONSE
curl -X GET http://localhost:8080/api/admin/guru | jq '.'

# GET SPECIFIC FIELD
curl -X GET http://localhost:8080/api/admin/guru | jq '.data[0].nama_guru'
```

---

**Created:** April 15, 2026  
**Updated:** April 15, 2026
