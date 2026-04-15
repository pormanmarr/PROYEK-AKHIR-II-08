package repository

import (
	"database/sql"
	"fmt"
	"tk_mutiara_backend/models"
)

// ==============================
// DASHBOARD REPOSITORY
// ==============================

// GetDashboardMetrics mengambil metrics dashboard
func GetDashboardMetrics(db *sql.DB) (*models.DashboardMetrics, error) {
	var metrics models.DashboardMetrics

	// Total siswa
	err := db.QueryRow("SELECT COUNT(*) FROM siswa").Scan(&metrics.TotalSiswa)
	if err != nil {
		return nil, fmt.Errorf("error mengambil total siswa: %v", err)
	}

	// Total guru
	err = db.QueryRow("SELECT COUNT(*) FROM guru").Scan(&metrics.TotalGuru)
	if err != nil {
		return nil, fmt.Errorf("error mengambil total guru: %v", err)
	}

	// Total kelas
	err = db.QueryRow("SELECT COUNT(*) FROM kelas").Scan(&metrics.TotalKelas)
	if err != nil {
		return nil, fmt.Errorf("error mengambil total kelas: %v", err)
	}

	// Total tagihan
	err = db.QueryRow("SELECT COUNT(*) FROM tagihan").Scan(&metrics.TotalTagihan)
	if err != nil {
		return nil, fmt.Errorf("error mengambil total tagihan: %v", err)
	}

	// Tagihan terbayar
	err = db.QueryRow("SELECT COUNT(*) FROM tagihan WHERE status = 'lunas'").Scan(&metrics.TagihanTerbayar)
	if err != nil {
		return nil, fmt.Errorf("error mengambil tagihan terbayar: %v", err)
	}

	// Tagihan belum bayar
	metrics.TagihanBelumBayar = metrics.TotalTagihan - metrics.TagihanTerbayar

	// Total pemasukan bulan ini
	err = db.QueryRow(`
		SELECT COALESCE(SUM(p.jumlah_bayar), 0) FROM pembayaran p
		WHERE STATUS_BAYAR = 'diterima' 
		AND MONTH(p.tgl_pembayaran) = MONTH(NOW())
		AND YEAR(p.tgl_pembayaran) = YEAR(NOW())
	`).Scan(&metrics.TotalPemasukanBulan)
	if err != nil {
		return nil, fmt.Errorf("error mengambil total pemasukan: %v", err)
	}

	// Total hutang bulan ini
	err = db.QueryRow(`
		SELECT COALESCE(SUM(t.jumlah_tagihan), 0) FROM tagihan t
		WHERE STATUS = 'belum_bayar'
		AND MONTH(t.created_at) = MONTH(NOW())
		AND YEAR(t.created_at) = YEAR(NOW())
	`).Scan(&metrics.TotalHutangBulan)
	if err != nil {
		return nil, fmt.Errorf("error mengambil total hutang: %v", err)
	}

	return &metrics, nil
}

// GetDashboardStatistics mengambil statistik dashboard per bulan
func GetDashboardStatistics(db *sql.DB, limit int) ([]models.DashboardStatistic, error) {
	rows, err := db.Query(`
		SELECT 
			DATE(p.tgl_pembayaran) as date,
			COUNT(*) as total_data,
			COALESCE(SUM(p.jumlah_bayar), 0) as revenue
		FROM pembayaran p
		WHERE p.status_bayar = 'diterima'
		GROUP BY DATE(p.tgl_pembayaran)
		ORDER BY DATE(p.tgl_pembayaran) DESC
		LIMIT ?
	`, limit)
	if err != nil {
		return nil, fmt.Errorf("error query statistik: %v", err)
	}
	defer rows.Close()

	var statistics []models.DashboardStatistic
	for rows.Next() {
		var stat models.DashboardStatistic
		err := rows.Scan(&stat.Date, &stat.TotalData, &stat.Revenue)
		if err != nil {
			return nil, fmt.Errorf("error scan statistik: %v", err)
		}
		stat.Status = "success"
		statistics = append(statistics, stat)
	}

	return statistics, nil
}

// ==============================
// GURU REPOSITORY
// ==============================

// GetAllGuru mengambil semua guru dengan detail
func GetAllGuru(db *sql.DB) ([]models.GuruDetail, error) {
	rows, err := db.Query(`
		SELECT 
			g.id_guru,
			g.nama_guru,
			g.no_hp,
			g.email,
			COUNT(DISTINCT k.id_kelas) as total_kelas,
			COUNT(DISTINCT s.nomor_induk_siswa) as total_siswa,
			g.created_at
		FROM guru g
		LEFT JOIN kelas k ON g.id_guru = k.id_guru
		LEFT JOIN siswa s ON k.id_kelas = s.id_kelas
		GROUP BY g.id_guru
		ORDER BY g.created_at DESC
	`)
	if err != nil {
		return nil, fmt.Errorf("error query guru: %v", err)
	}
	defer rows.Close()

	var gurus []models.GuruDetail
	for rows.Next() {
		var guru models.GuruDetail
		err := rows.Scan(
			&guru.IDGuru,
			&guru.NamaGuru,
			&guru.NoHP,
			&guru.Email,
			&guru.TotalKelas,
			&guru.TotalSiswa,
			&guru.CreatedAt,
		)
		if err != nil {
			return nil, fmt.Errorf("error scan guru: %v", err)
		}
		gurus = append(gurus, guru)
	}

	return gurus, nil
}

// GetGuruByID mengambil guru berdasarkan ID
func GetGuruByID(db *sql.DB, idGuru int) (*models.GuruDetail, error) {
	var guru models.GuruDetail
	err := db.QueryRow(`
		SELECT 
			g.id_guru,
			g.nama_guru,
			g.no_hp,
			g.email,
			COUNT(DISTINCT k.id_kelas) as total_kelas,
			COUNT(DISTINCT s.nomor_induk_siswa) as total_siswa,
			g.created_at
		FROM guru g
		LEFT JOIN kelas k ON g.id_guru = k.id_guru
		LEFT JOIN siswa s ON k.id_kelas = s.id_kelas
		WHERE g.id_guru = ?
		GROUP BY g.id_guru
	`, idGuru).Scan(
		&guru.IDGuru,
		&guru.NamaGuru,
		&guru.NoHP,
		&guru.Email,
		&guru.TotalKelas,
		&guru.TotalSiswa,
		&guru.CreatedAt,
	)
	if err != nil {
		return nil, fmt.Errorf("error query guru: %v", err)
	}

	return &guru, nil
}

// CreateGuru membuat guru baru
func CreateGuru(db *sql.DB, guru *models.CreateGuruRequest) (int64, error) {
	result, err := db.Exec(
		"INSERT INTO guru (nama_guru, no_hp, email, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())",
		guru.NamaGuru,
		guru.NoHP,
		guru.Email,
	)
	if err != nil {
		return 0, fmt.Errorf("error create guru: %v", err)
	}

	return result.LastInsertId()
}

// UpdateGuru update data guru
func UpdateGuru(db *sql.DB, idGuru int, guru *models.UpdateGuruRequest) error {
	_, err := db.Exec(
		"UPDATE guru SET nama_guru = ?, no_hp = ?, email = ?, updated_at = NOW() WHERE id_guru = ?",
		guru.NamaGuru,
		guru.NoHP,
		guru.Email,
		idGuru,
	)
	if err != nil {
		return fmt.Errorf("error update guru: %v", err)
	}

	return nil
}

// DeleteGuru delete guru
func DeleteGuru(db *sql.DB, idGuru int) error {
	_, err := db.Exec("DELETE FROM guru WHERE id_guru = ?", idGuru)
	if err != nil {
		return fmt.Errorf("error delete guru: %v", err)
	}

	return nil
}
