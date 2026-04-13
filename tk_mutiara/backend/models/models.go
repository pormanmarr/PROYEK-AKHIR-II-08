package models

// LoginRequest request untuk login
type LoginRequest struct {
	Email    string `json:"email" binding:"required,email"`
	Password string `json:"password" binding:"required,min=6"`
}

// User model untuk user data
type User struct {
	ID       int    `json:"id"`
	Email    string `json:"email"`
	Password string `json:"-"` // jangan expose password
	NamaAnak string `json:"nama_anak"`
	Kelas    string `json:"kelas"`
	Role     string `json:"role"`
}

// Pengumuman model untuk announcements
type Pengumuman struct {
	ID       int    `json:"id" db:"id"`
	Judul    string `json:"judul" db:"judul"`
	Isi      string `json:"isi" db:"isi"`
	Tanggal  string `json:"tanggal" db:"tanggal"`
	Kategori string `json:"kategori" db:"kategori"`
	IsRead   bool   `json:"is_read" db:"is_read"` // capai untuk track read status
}

// Perkembangan model untuk development tracking
type Perkembangan struct {
	ID            int     `json:"id" db:"id"`
	NamaAnak      string  `json:"nama_anak" db:"nama_anak"`
	Tanggal       string  `json:"tanggal" db:"tanggal"`
	Kategori      string  `json:"kategori" db:"kategori"`
	Deskripsi     string  `json:"deskripsi" db:"deskripsi"`
	NilaiKognitif float64 `json:"nilai_kognitif" db:"nilai_kognitif"`
	NilaiMotorik  float64 `json:"nilai_motorik" db:"nilai_motorik"`
	NilaiSosial   float64 `json:"nilai_sosial" db:"nilai_sosial"`
	NilaiBahasa   float64 `json:"nilai_bahasa" db:"nilai_bahasa"`
	NilaiSeni     float64 `json:"nilai_seni" db:"nilai_seni"`
	Catatan       string  `json:"catatan" db:"catatan"`
}

// Pembayaran model untuk payment tracking
type Pembayaran struct {
	ID                string `json:"id" db:"id"`
	Bulan             string `json:"bulan" db:"bulan"`
	Tahun             string `json:"tahun" db:"tahun"`
	Nominal           int    `json:"nominal" db:"nominal"`
	Status            string `json:"status" db:"status"` // "lunas" atau "belum"
	TanggalBayar      string `json:"tanggal_bayar" db:"tanggal_bayar"`
	MetodePembayaran  string `json:"metode_pembayaran" db:"metode_pembayaran"`
	KodeTransaksi     string `json:"kode_transaksi" db:"kode_transaksi"`
}

// BayarRequest request untuk pembayaran
type BayarRequest struct {
	ID     string `json:"id" binding:"required"`
	Metode string `json:"metode" binding:"required"`
}

// ApiResponse generic response wrapper
type ApiResponse struct {
	Success bool        `json:"success"`
	Message string      `json:"message,omitempty"`
	Data    interface{} `json:"data,omitempty"`
	Error   string      `json:"error,omitempty"`
}
