package services

import (
	"database/sql"
	"tk_mutiara_backend/models"
	"tk_mutiara_backend/repository"
)

// GetAllPengumuman mengambil semua pengumuman dari service layer
func GetAllPengumuman(db *sql.DB) ([]models.Pengumuman, error) {
	return repository.GetAllPengumuman(db)
}

// GetPengumumanByID mengambil pengumuman berdasarkan ID dari service layer
func GetPengumumanByID(db *sql.DB, id int64) (models.Pengumuman, error) {
	return repository.GetPengumumanByID(db, id)
}
