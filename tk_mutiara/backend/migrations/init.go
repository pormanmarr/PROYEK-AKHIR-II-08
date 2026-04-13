package migrations

import (
	"database/sql"
	"log"
)

// InitializeSchema membuat tabel-tabel yang diperlukan
func InitializeSchema(db *sql.DB) error {
	// Create users table
	usersTable := `
	CREATE TABLE IF NOT EXISTS users (
		id SERIAL PRIMARY KEY,
		email VARCHAR(255) UNIQUE NOT NULL,
		password VARCHAR(255) NOT NULL,
		nama_anak VARCHAR(255) NOT NULL,
		kelas VARCHAR(100) NOT NULL,
		role VARCHAR(50) DEFAULT 'orangtua',
		created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
	);
	`

	// Create pengumuman table
	pengumumanTable := `
	CREATE TABLE IF NOT EXISTS pengumuman (
		id SERIAL PRIMARY KEY,
		judul VARCHAR(255) NOT NULL,
		isi TEXT NOT NULL,
		tanggal VARCHAR(50),
		kategori VARCHAR(50),
		is_read BOOLEAN DEFAULT false,
		created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
		updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
	);
	`

	// Create perkembangan table
	perkembanganTable := `
	CREATE TABLE IF NOT EXISTS perkembangan (
		id SERIAL PRIMARY KEY,
		nama_anak VARCHAR(255) NOT NULL,
		tanggal VARCHAR(50),
		kategori VARCHAR(100),
		deskripsi TEXT,
		nilai_kognitif DECIMAL(5, 2),
		nilai_motorik DECIMAL(5, 2),
		nilai_sosial DECIMAL(5, 2),
		nilai_bahasa DECIMAL(5, 2),
		nilai_seni DECIMAL(5, 2),
		catatan TEXT,
		created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
		updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
	);
	`

	// Create pembayaran table
	pembayaranTable := `
	CREATE TABLE IF NOT EXISTS pembayaran (
		id VARCHAR(50) PRIMARY KEY,
		bulan VARCHAR(50) NOT NULL,
		tahun VARCHAR(4) NOT NULL,
		nominal INTEGER NOT NULL,
		status VARCHAR(50) DEFAULT 'belum',
		tanggal_bayar VARCHAR(50),
		metode_pembayaran VARCHAR(100),
		kode_transaksi VARCHAR(100),
		created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
		updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
	);
	`

	tables := []string{usersTable, pengumumanTable, perkembanganTable, pembayaranTable}

	for _, table := range tables {
		if _, err := db.Exec(table); err != nil {
			return err
		}
	}

	log.Println("✓ Schema berhasil diinisialisasi")
	return nil
}
