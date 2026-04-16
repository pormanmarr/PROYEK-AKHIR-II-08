package services

import (
	"strconv"
	"tk_mutiara_backend/models"
	"tk_mutiara_backend/repository"
)

// PerkembanganService service untuk perkembangan
type PerkembanganService struct {
	repo repository.PerkembanganRepository
}

// NewPerkembanganService membuat instance baru
func NewPerkembanganService(repo repository.PerkembanganRepository) *PerkembanganService {
	return &PerkembanganService{repo: repo}
}

// GetAll mengambil semua perkembangan
func (s *PerkembanganService) GetAll() ([]models.Perkembangan, error) {
	return s.repo.GetAll()
}

// GetByID mengambil perkembangan berdasarkan ID
func (s *PerkembanganService) GetByID(id string) (*models.Perkembangan, error) {
	intID, err := strconv.Atoi(id)
	if err != nil {
		return nil, err
	}
	return s.repo.GetByID(intID)
}

// GetByNomorInduk mengambil perkembangan berdasarkan nomor induk siswa
func (s *PerkembanganService) GetByNomorInduk(nomorInduk string) ([]models.Perkembangan, error) {
	return s.repo.GetByNomorInduk(nomorInduk)
}
