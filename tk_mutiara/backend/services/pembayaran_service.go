package services

import (
	"fmt"
	"time"
	"tk_mutiara_backend/models"
	"tk_mutiara_backend/repository"
)

// PembayaranService service untuk pembayaran
type PembayaranService struct {
	repo repository.PembayaranRepository
}

// NewPembayaranService membuat instance baru
func NewPembayaranService(repo repository.PembayaranRepository) *PembayaranService {
	return &PembayaranService{repo: repo}
}

// GetAll mengambil semua pembayaran
func (s *PembayaranService) GetAll() ([]models.Pembayaran, error) {
	return s.repo.GetAll()
}

// GetByID mengambil pembayaran berdasarkan ID
func (s *PembayaranService) GetByID(id string) (*models.Pembayaran, error) {
	return s.repo.GetByID(id)
}

// ProcessPayment memproses pembayaran
func (s *PembayaranService) ProcessPayment(id string, metode string) (map[string]interface{}, error) {
	// Get pembayaran yang akan dibayar
	pembayaran, err := s.repo.GetByID(id)
	if err != nil {
		return nil, err
	}

	// Generate kode transaksi
	kodeTransaksi := fmt.Sprintf("TRX-%s-%s", time.Now().Format("20060102150405"), id)

	// Update pembayaran
	pembayaran.Status = "lunas"
	pembayaran.TanggalBayar = time.Now().Format("02 Jan 2006")
	pembayaran.MetodePembayaran = metode
	pembayaran.KodeTransaksi = kodeTransaksi

	if err := s.repo.Update(pembayaran); err != nil {
		return nil, err
	}

	return map[string]interface{}{
		"kode_transaksi": kodeTransaksi,
		"status":         "lunas",
		"tanggal_bayar":  pembayaran.TanggalBayar,
		"metode":         metode,
	}, nil
}
