package services

import (
	"strconv"
	"tk_mutiara_backend/models"
	"tk_mutiara_backend/repository"
)

// PengumumanService interface untuk service methods
type PengumumanService struct {
	repo repository.PengumumanRepository
}

// NewPengumumanService membuat instance baru
func NewPengumumanService(repo repository.PengumumanRepository) *PengumumanService {
	return &PengumumanService{repo: repo}
}

// GetAll mengambil semua pengumuman
func (s *PengumumanService) GetAll() ([]models.Pengumuman, error) {
	return s.repo.GetAll()
}

// GetByID mengambil pengumuman berdasarkan ID
func (s *PengumumanService) GetByID(id string) (*models.Pengumuman, error) {
	intID, err := strconv.Atoi(id)
	if err != nil {
		return nil, err
	}
	return s.repo.GetByID(intID)
}

// MarkAsRead menandai pengumuman sebagai sudah dibaca
func (s *PengumumanService) MarkAsRead(id string) error {
	intID, err := strconv.Atoi(id)
	if err != nil {
		return err
	}
	return s.repo.MarkAsRead(intID)
}
