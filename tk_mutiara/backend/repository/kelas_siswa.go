package repository

import (
	"database/sql"
	"fmt"
	"tk_mutiara_backend/models"
)

// ==============================
// KELAS REPOSITORY
// ==============================

// GetAllKelas mengambil semua kelas
func GetAllKelas(db *sql.DB) ([]models.KelasDetail, error) {
	rows, err := db.Query(`
		SELECT 
			k.id_kelas,
			k.nama_kelas,
			g.nama_guru,
			COUNT(DISTINCT s.nomor_induk_siswa) as total_siswa,
			k.created_at
		FROM kelas k
		LEFT JOIN guru g ON k.id_guru = g.id_guru
		LEFT JOIN siswa s ON k.id_kelas = s.id_kelas
		GROUP BY k.id_kelas
		ORDER BY k.created_at DESC
	`)
	if err != nil {
		return nil, fmt.Errorf("error query kelas: %v", err)
	}
	defer rows.Close()

	var kelas []models.KelasDetail
	for rows.Next() {
		var k models.KelasDetail
		err := rows.Scan(&k.IDKelas, &k.NamaKelas, &k.NamaGuru, &k.TotalSiswa, &k.CreatedAt)
		if err != nil {
			return nil, fmt.Errorf("error scan kelas: %v", err)
		}
		kelas = append(kelas, k)
	}

	return kelas, nil
}

// GetKelasByID mengambil kelas berdasarkan ID
func GetKelasByID(db *sql.DB, idKelas int) (*models.KelasDetail, error) {
	var k models.KelasDetail
	err := db.QueryRow(`
		SELECT 
			k.id_kelas,
			k.nama_kelas,
			g.nama_guru,
			COUNT(DISTINCT s.nomor_induk_siswa) as total_siswa,
			k.created_at
		FROM kelas k
		LEFT JOIN guru g ON k.id_guru = g.id_guru
		LEFT JOIN siswa s ON k.id_kelas = s.id_kelas
		WHERE k.id_kelas = ?
		GROUP BY k.id_kelas
	`, idKelas).Scan(&k.IDKelas, &k.NamaKelas, &k.NamaGuru, &k.TotalSiswa, &k.CreatedAt)
	if err != nil {
		return nil, fmt.Errorf("error query kelas: %v", err)
	}

	return &k, nil
}

// CreateKelas membuat kelas baru
func CreateKelas(db *sql.DB, kelas *models.CreateKelasRequest) (int64, error) {
	result, err := db.Exec(
		"INSERT INTO kelas (id_guru, nama_kelas, created_at, updated_at) VALUES (?, ?, NOW(), NOW())",
		kelas.IDGuru,
		kelas.NamaKelas,
	)
	if err != nil {
		return 0, fmt.Errorf("error create kelas: %v", err)
	}

	return result.LastInsertId()
}

// DeleteKelas delete kelas
func DeleteKelas(db *sql.DB, idKelas int) error {
	_, err := db.Exec("DELETE FROM kelas WHERE id_kelas = ?", idKelas)
	if err != nil {
		return fmt.Errorf("error delete kelas: %v", err)
	}

	return nil
}

// ==============================
// SISWA REPOSITORY
// ==============================

// GetAllSiswa mengambil semua siswa dengan detail
func GetAllSiswa(db *sql.DB) ([]models.SiswaDetail, error) {
	rows, err := db.Query(`
		SELECT 
			s.nomor_induk_siswa,
			s.nama_siswa AS nama_anak,
			s.nama_orgtua,
			k.nama_kelas,
			s.jenis_kelamin,
			s.tgl_lahir,
			s.alamat,
			COALESCE(SUM(t.jumlah_tagihan), 0) as total_tagihan,
			COALESCE(SUM(CASE WHEN p.status_bayar = 'diterima' THEN p.jumlah_bayar ELSE 0 END), 0) as total_bayar,
			COALESCE(SUM(CASE WHEN t.status = 'belum_bayar' THEN t.jumlah_tagihan ELSE 0 END), 0) as sisa_tagihan,
			CASE 
				WHEN COALESCE(SUM(CASE WHEN t.status = 'belum_bayar' THEN t.jumlah_tagihan ELSE 0 END), 0) > 0 THEN 'Belum Lunas'
				ELSE 'Lunas'
			END as status_pembayaran,
			s.created_at
		FROM siswa s
		LEFT JOIN kelas k ON s.id_kelas = k.id_kelas
		LEFT JOIN tagihan t ON s.nomor_induk_siswa = t.nomor_induk_siswa
		LEFT JOIN pembayaran p ON t.id_tagihan = p.id_tagihan
		GROUP BY s.nomor_induk_siswa
		ORDER BY s.created_at DESC
	`)
	if err != nil {
		return nil, fmt.Errorf("error query siswa: %v", err)
	}
	defer rows.Close()

	var siswa []models.SiswaDetail
	for rows.Next() {
		var s models.SiswaDetail
		err := rows.Scan(
			&s.NomorIndukSiswa,
			&s.NamaAnak,
			&s.NamaOrgTua,
			&s.Kelas,
			&s.JenisKelamin,
			&s.TglLahir,
			&s.Alamat,
			&s.TotalTagihan,
			&s.TotalBayar,
			&s.SisaTagihan,
			&s.StatusPembayaran,
			&s.CreatedAt,
		)
		if err != nil {
			return nil, fmt.Errorf("error scan siswa: %v", err)
		}
		siswa = append(siswa, s)
	}

	return siswa, nil
}

// GetSiswaByID mengambil siswa berdasarkan ID
func GetSiswaByID(db *sql.DB, nomorIndukSiswa string) (*models.SiswaDetail, error) {
	var s models.SiswaDetail
	err := db.QueryRow(`
		SELECT 
			s.nomor_induk_siswa,
			s.nama_siswa AS nama_anak,
			s.nama_orgtua,
			k.nama_kelas,
			s.jenis_kelamin,
			s.tgl_lahir,
			s.alamat,
			COALESCE(SUM(t.jumlah_tagihan), 0) as total_tagihan,
			COALESCE(SUM(CASE WHEN p.status_bayar = 'diterima' THEN p.jumlah_bayar ELSE 0 END), 0) as total_bayar,
			COALESCE(SUM(CASE WHEN t.status = 'belum_bayar' THEN t.jumlah_tagihan ELSE 0 END), 0) as sisa_tagihan,
			CASE 
				WHEN COALESCE(SUM(CASE WHEN t.status = 'belum_bayar' THEN t.jumlah_tagihan ELSE 0 END), 0) > 0 THEN 'Belum Lunas'
				ELSE 'Lunas'
			END as status_pembayaran,
			s.created_at
		FROM siswa s
		LEFT JOIN kelas k ON s.id_kelas = k.id_kelas
		LEFT JOIN tagihan t ON s.nomor_induk_siswa = t.nomor_induk_siswa
		LEFT JOIN pembayaran p ON t.id_tagihan = p.id_tagihan
		WHERE s.nomor_induk_siswa = ?
		GROUP BY s.nomor_induk_siswa
	`, nomorIndukSiswa).Scan(
		&s.NomorIndukSiswa,
		&s.NamaAnak,
		&s.NamaOrgTua,
		&s.Kelas,
		&s.JenisKelamin,
		&s.TglLahir,
		&s.Alamat,
		&s.TotalTagihan,
		&s.TotalBayar,
		&s.SisaTagihan,
		&s.StatusPembayaran,
		&s.CreatedAt,
	)
	if err != nil {
		return nil, fmt.Errorf("error query siswa: %v", err)
	}

	return &s, nil
}

// GetSiswaByKelas mengambil siswa berdasarkan kelas
func GetSiswaByKelas(db *sql.DB, idKelas int) ([]models.SiswaDetail, error) {
	rows, err := db.Query(`
		SELECT 
			s.nomor_induk_siswa,
			s.nama_siswa AS nama_anak,
			s.nama_orgtua,
			k.nama_kelas,
			s.jenis_kelamin,
			s.tgl_lahir,
			s.alamat,
			COALESCE(SUM(t.jumlah_tagihan), 0) as total_tagihan,
			COALESCE(SUM(CASE WHEN p.status_bayar = 'diterima' THEN p.jumlah_bayar ELSE 0 END), 0) as total_bayar,
			COALESCE(SUM(CASE WHEN t.status = 'belum_bayar' THEN t.jumlah_tagihan ELSE 0 END), 0) as sisa_tagihan,
			CASE 
				WHEN COALESCE(SUM(CASE WHEN t.status = 'belum_bayar' THEN t.jumlah_tagihan ELSE 0 END), 0) > 0 THEN 'Belum Lunas'
				ELSE 'Lunas'
			END as status_pembayaran,
			s.created_at
		FROM siswa s
		LEFT JOIN kelas k ON s.id_kelas = k.id_kelas
		LEFT JOIN tagihan t ON s.nomor_induk_siswa = t.nomor_induk_siswa
		LEFT JOIN pembayaran p ON t.id_tagihan = p.id_tagihan
		WHERE s.id_kelas = ?
		GROUP BY s.nomor_induk_siswa
		ORDER BY s.created_at DESC
	`, idKelas)
	if err != nil {
		return nil, fmt.Errorf("error query siswa: %v", err)
	}
	defer rows.Close()

	var siswa []models.SiswaDetail
	for rows.Next() {
		var s models.SiswaDetail
		err := rows.Scan(
			&s.NomorIndukSiswa,
			&s.NamaAnak,
			&s.NamaOrgTua,
			&s.Kelas,
			&s.JenisKelamin,
			&s.TglLahir,
			&s.Alamat,
			&s.TotalTagihan,
			&s.TotalBayar,
			&s.SisaTagihan,
			&s.StatusPembayaran,
			&s.CreatedAt,
		)
		if err != nil {
			return nil, fmt.Errorf("error scan siswa: %v", err)
		}
		siswa = append(siswa, s)
	}

	return siswa, nil
}

// CreateSiswa membuat siswa baru
func CreateSiswa(db *sql.DB, siswa *models.CreateSiswaRequest) (int64, error) {
	result, err := db.Exec(`
		INSERT INTO siswa (id_kelas, nama_orgtua, tgl_lahir, jenis_kelamin, alamat, created_at, updated_at) 
		VALUES (?, ?, ?, ?, ?, NOW(), NOW())
	`,
		siswa.IDKelas,
		siswa.NamaOrgTua,
		siswa.TglLahir,
		siswa.JenisKelamin,
		siswa.Alamat,
	)
	if err != nil {
		return 0, fmt.Errorf("error create siswa: %v", err)
	}

	return result.LastInsertId()
}

// DeleteSiswa delete siswa
func DeleteSiswa(db *sql.DB, nomorIndukSiswa string) error {
	_, err := db.Exec("DELETE FROM siswa WHERE nomor_induk_siswa = ?", nomorIndukSiswa)
	if err != nil {
		return fmt.Errorf("error delete siswa: %v", err)
	}

	return nil
}
