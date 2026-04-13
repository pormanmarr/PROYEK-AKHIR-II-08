# Backend TK Mutiara

Backend API untuk aplikasi TK Mutiara dibangun dengan Go menggunakan Gin framework dan PostgreSQL database.

## Struktur Project

```
backend/
├── config/                 # Configuration files
│   ├── config.go          # Environment config loader
│   └── database.go        # Database connection
├── middleware/            # Middleware functions
│   └── auth.go            # JWT authentication
├── models/                # Data models
│   └── models.go          # All model definitions
├── migrations/            # Database migrations
│   └── init.go            # Schema initialization
├── repository/            # Data layer
│   ├── pengumuman.go      # Pengumuman repository
│   ├── perkembangan.go    # Perkembangan repository
│   └── pembayaran.go      # Pembayaran repository
├── services/              # Business logic layer
│   ├── pengumuman_service.go
│   ├── perkembangan_service.go
│   └── pembayaran_service.go
├── .env.example           # Environment template
├── go.mod                 # Go modules
├── go.sum                 # Go sum file
└── main.go                # Entry point
```

## Setup & Installation

### 1. Prerequisites
- Go 1.25.6 atau lebih tinggi
- PostgreSQL 12 atau lebih tinggi
- Git

### 2. Setup Database PostgreSQL

```bash
# Buat database baru
createdb tk_mutiara

# Atau via psql
psql -U postgres
CREATE DATABASE tk_mutiara;
```

### 3. Clone & Setup Project

```bash
cd backend

# Copy environment template
cp .env.example .env

# Edit .env dengan konfigurasi Anda
nano .env
```

### 4. Environment Configuration (.env)

```env
# Database Configuration
DB_HOST=localhost
DB_PORT=5432
DB_USER=postgres
DB_PASSWORD=your_password
DB_NAME=tk_mutiara
DB_SSL_MODE=disable

# Server Configuration
SERVER_PORT=8081
GIN_MODE=debug

# JWT Configuration
JWT_SECRET=tk_mutiara_secret_key
JWT_EXPIRATION=24

# Environment
ENVIRONMENT=development
```

### 5. Install Dependencies

```bash
go mod download
```

### 6. Run Backend

```bash
go run main.go
```

Server akan berjalan di `http://localhost:8081`

## Database Schema

### Users Table
```sql
- id (SERIAL PRIMARY KEY)
- email (VARCHAR UNIQUE)
- password (VARCHAR)
- nama_anak (VARCHAR)
- kelas (VARCHAR)
- role (VARCHAR DEFAULT 'orangtua')
- created_at (TIMESTAMP)
```

### Pengumuman Table
```sql
- id (SERIAL PRIMARY KEY)
- judul (VARCHAR)
- isi (TEXT)
- tanggal (VARCHAR)
- kategori (VARCHAR)
- is_read (BOOLEAN DEFAULT false)
- created_at, updated_at (TIMESTAMP)
```

### Perkembangan Table
```sql
- id (SERIAL PRIMARY KEY)
- nama_anak (VARCHAR)
- tanggal (VARCHAR)
- kategori (VARCHAR)
- deskripsi (TEXT)
- nilai_kognitif, nilai_motorik, nilai_sosial, nilai_bahasa, nilai_seni (DECIMAL)
- catatan (TEXT)
- created_at, updated_at (TIMESTAMP)
```

### Pembayaran Table
```sql
- id (VARCHAR PRIMARY KEY)
- bulan (VARCHAR)
- tahun (VARCHAR)
- nominal (INTEGER)
- status (VARCHAR DEFAULT 'belum')
- tanggal_bayar, metode_pembayaran, kode_transaksi (VARCHAR)
- created_at, updated_at (TIMESTAMP)
```

## API Endpoints

### Public Routes

#### Login
```
POST /login
Content-Type: application/json

{
  "email": "orangtua@tkmutiara.com",
  "password": "mutiara123"
}

Response:
{
  "success": true,
  "data": {
    "token": "jwt_token_here",
    "user": {
      "nama_anak": "Bintang Mutiara",
      "kelas": "Kelompok A",
      "email": "orangtua@tkmutiara.com"
    }
  }
}
```

### Protected Routes (Require JWT Token)

**Header:**
```
Authorization: Bearer {jwt_token}
```

#### Pengumuman

```
GET /api/pengumuman
- Mengambil semua pengumuman

GET /api/pengumuman/:id
- Mengambil detail pengumuman berdasarkan ID

POST /api/pengumuman/:id/read
- Menandai pengumuman sebagai sudah dibaca
```

#### Perkembangan

```
GET /api/perkembangan
- Mengambil semua perkembangan anak

GET /api/perkembangan/:id
- Mengambil detail perkembangan berdasarkan ID
```

#### Pembayaran

```
GET /api/pembayaran
- Mengambil semua riwayat pembayaran

GET /api/pembayaran/:id
- Mengambil detail pembayaran berdasarkan ID

POST /api/pembayaran/bayar
Content-Type: application/json

{
  "id": "4",
  "metode": "Transfer Bank"
}

Response:
{
  "success": true,
  "message": "Pembayaran berhasil diproses",
  "data": {
    "kode_transaksi": "TRX-20260403120000-4",
    "status": "lunas",
    "tanggal_bayar": "03 Apr 2026",
    "metode": "Transfer Bank"
  }
}
```

## Development Workflow

### Add New Feature

1. **Create Model** di `models/models.go`
2. **Create Repository** di `repository/`
3. **Create Service** di `services/`
4. **Add Routes** di `main.go`

### Testing

```bash
# Test dengan curl
curl -X GET http://localhost:8081/

# Login untuk dapat token
curl -X POST http://localhost:8081/login \
  -H "Content-Type: application/json" \
  -d '{"email":"orangtua@tkmutiara.com","password":"mutiara123"}'

# Gunakan token untuk protected routes
curl -X GET http://localhost:8081/api/pengumuman \
  -H "Authorization: Bearer {token}"
```

## Future Improvements

- [ ] Tambahkan authentication dengan username/password dari database
- [ ] Implementasi role-based access control
- [ ] Tambahkan file upload untuk foto profil
- [ ] Implementasi pagination untuk list endpoints
- [ ] Tambahkan logging system
- [ ] Implementasi caching dengan Redis
- [ ] Tambahkan unit tests
- [ ] Setup CI/CD pipeline
- [ ] Implementasi email notifications
- [ ] Tambahkan payment gateway integration

## Dependencies

- `github.com/gin-gonic/gin` - Web framework
- `github.com/golang-jwt/jwt/v5` - JWT authentication
- `github.com/lib/pq` - PostgreSQL driver
- `github.com/joho/godotenv` - .env loader

## Troubleshooting

### Database Connection Error
- Pastikan PostgreSQL sudah running
- Cek konfigurasi di `.env` file
- Pastikan database `tk_mutiara` sudah dibuat

### Port Already in Use
- Ubah `SERVER_PORT` di `.env` file
- Atau kill process yang menggunakan port

### Missing Dependencies
```bash
go get -u ./...
go mod tidy
```
