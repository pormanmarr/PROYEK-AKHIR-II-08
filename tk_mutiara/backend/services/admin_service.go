package services

import (
	"database/sql"
	"fmt"
	"tk_mutiara_backend/models"
	"tk_mutiara_backend/repository"
)

// ==============================
// DASHBOARD SERVICE
// ==============================

// GetDashboardOverview mengambil overview dashboard
func GetDashboardOverview(db *sql.DB) (*models.DashboardMetrics, error) {
	metrics, err := repository.GetDashboardMetrics(db)
	if err != nil {
		return nil, err
	}

	return metrics, nil
}

// GetDashboardStatistics mengambil statistik dashboard
func GetDashboardStatistics(db *sql.DB, limit int) ([]models.DashboardStatistic, error) {
	if limit <= 0 {
		limit = 30 // default 30 hari
	}

	statistics, err := repository.GetDashboardStatistics(db, limit)
	if err != nil {
		return nil, err
	}

	return statistics, nil
}

// ==============================
// GURU SERVICE
// ==============================

// GetAllGurus mengambil semua guru dengan validasi
func GetAllGurus(db *sql.DB) ([]models.GuruDetail, error) {
	gurus, err := repository.GetAllGuru(db)
	if err != nil {
		return nil, err
	}

	return gurus, nil
}

// GetGuruDetail mengambil detail guru berdasarkan ID
func GetGuruDetail(db *sql.DB, idGuru int) (*models.GuruDetail, error) {
	if idGuru <= 0 {
		return nil, fmt.Errorf("guru ID tidak valid")
	}

	guru, err := repository.GetGuruByID(db, idGuru)
	if err != nil {
		return nil, err
	}

	return guru, nil
}

// CreateNewGuru membuat guru baru dengan validasi
func CreateNewGuru(db *sql.DB, guruReq *models.CreateGuruRequest) (int64, error) {
	// Validasi input
	if guruReq.NamaGuru == "" {
		return 0, fmt.Errorf("nama guru tidak boleh kosong")
	}
	if guruReq.Email == "" {
		return 0, fmt.Errorf("email tidak boleh kosong")
	}
	if guruReq.NoHP == "" {
		return 0, fmt.Errorf("nomor HP tidak boleh kosong")
	}

	// Create guru
	id, err := repository.CreateGuru(db, guruReq)
	if err != nil {
		return 0, err
	}

	return id, nil
}

// UpdateGuruData update data guru dengan validasi
func UpdateGuruData(db *sql.DB, idGuru int, guruReq *models.UpdateGuruRequest) error {
	if idGuru <= 0 {
		return fmt.Errorf("guru ID tidak valid")
	}

	// Cek guru exist
	_, err := repository.GetGuruByID(db, idGuru)
	if err != nil {
		return fmt.Errorf("guru tidak ditemukan")
	}

	// Update
	err = repository.UpdateGuru(db, idGuru, guruReq)
	if err != nil {
		return err
	}

	return nil
}

// RemoveGuru menghapus guru
func RemoveGuru(db *sql.DB, idGuru int) error {
	if idGuru <= 0 {
		return fmt.Errorf("guru ID tidak valid")
	}

	// Cek guru exist
	_, err := repository.GetGuruByID(db, idGuru)
	if err != nil {
		return fmt.Errorf("guru tidak ditemukan")
	}

	// Delete
	err = repository.DeleteGuru(db, idGuru)
	if err != nil {
		return err
	}

	return nil
}

// ==============================
// KELAS SERVICE
// ==============================

// GetAllKelas mengambil semua kelas
func GetAllKelas(db *sql.DB) ([]models.KelasDetail, error) {
	kelas, err := repository.GetAllKelas(db)
	if err != nil {
		return nil, err
	}

	return kelas, nil
}

// GetKelasDetail mengambil detail kelas berdasarkan ID
func GetKelasDetail(db *sql.DB, idKelas int) (*models.KelasDetail, error) {
	if idKelas <= 0 {
		return nil, fmt.Errorf("kelas ID tidak valid")
	}

	kelas, err := repository.GetKelasByID(db, idKelas)
	if err != nil {
		return nil, err
	}

	return kelas, nil
}

// CreateNewKelas membuat kelas baru
func CreateNewKelas(db *sql.DB, kelasReq *models.CreateKelasRequest) (int64, error) {
	if kelasReq.IDGuru <= 0 {
		return 0, fmt.Errorf("guru ID tidak valid")
	}
	if kelasReq.NamaKelas == "" {
		return 0, fmt.Errorf("nama kelas tidak boleh kosong")
	}

	// Cek guru exist
	_, err := repository.GetGuruByID(db, kelasReq.IDGuru)
	if err != nil {
		return 0, fmt.Errorf("guru tidak ditemukan")
	}

	// Create kelas
	id, err := repository.CreateKelas(db, kelasReq)
	if err != nil {
		return 0, err
	}

	return id, nil
}

// RemoveKelas menghapus kelas
func RemoveKelas(db *sql.DB, idKelas int) error {
	if idKelas <= 0 {
		return fmt.Errorf("kelas ID tidak valid")
	}

	// Cek kelas exist
	_, err := repository.GetKelasByID(db, idKelas)
	if err != nil {
		return fmt.Errorf("kelas tidak ditemukan")
	}

	// Delete
	err = repository.DeleteKelas(db, idKelas)
	if err != nil {
		return err
	}

	return nil
}
