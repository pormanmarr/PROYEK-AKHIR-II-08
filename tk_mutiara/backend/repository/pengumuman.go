package repository

import (
	"database/sql"
	"errors"
	"tk_mutiara_backend/config"
	"tk_mutiara_backend/models"
)

// PengumumanRepository interface untuk pengumuman operations
type PengumumanRepository interface {
	GetAll() ([]models.Pengumuman, error)
	GetByID(id int) (*models.Pengumuman, error)
	Create(p *models.Pengumuman) error
	Update(p *models.Pengumuman) error
	Delete(id int) error
	MarkAsRead(id int) error
}

// pengumumanRepo implementasi repository
type pengumumanRepo struct {
	db *sql.DB
}

// NewPengumumanRepository membuat instance baru
func NewPengumumanRepository() PengumumanRepository {
	return &pengumumanRepo{db: config.DB}
}

// GetAll mengambil semua pengumuman
func (r *pengumumanRepo) GetAll() ([]models.Pengumuman, error) {
	query := `SELECT id, judul, isi, tanggal, kategori, is_read FROM pengumuman ORDER BY tanggal DESC`
	
	rows, err := r.db.Query(query)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var pengumuman []models.Pengumuman
	for rows.Next() {
		var p models.Pengumuman
		if err := rows.Scan(&p.ID, &p.Judul, &p.Isi, &p.Tanggal, &p.Kategori, &p.IsRead); err != nil {
			return nil, err
		}
		pengumuman = append(pengumuman, p)
	}

	return pengumuman, nil
}

// GetByID mengambil pengumuman berdasarkan ID
func (r *pengumumanRepo) GetByID(id int) (*models.Pengumuman, error) {
	query := `SELECT id, judul, isi, tanggal, kategori, is_read FROM pengumuman WHERE id = $1`
	
	var p models.Pengumuman
	err := r.db.QueryRow(query, id).Scan(&p.ID, &p.Judul, &p.Isi, &p.Tanggal, &p.Kategori, &p.IsRead)
	if err != nil {
		if err == sql.ErrNoRows {
			return nil, errors.New("pengumuman tidak ditemukan")
		}
		return nil, err
	}

	return &p, nil
}

// Create membuat pengumuman baru
func (r *pengumumanRepo) Create(p *models.Pengumuman) error {
	query := `INSERT INTO pengumuman (judul, isi, tanggal, kategori) VALUES ($1, $2, $3, $4) RETURNING id`
	err := r.db.QueryRow(query, p.Judul, p.Isi, p.Tanggal, p.Kategori).Scan(&p.ID)
	return err
}

// Update mengupdate pengumuman
func (r *pengumumanRepo) Update(p *models.Pengumuman) error {
	query := `UPDATE pengumuman SET judul = $1, isi = $2, tanggal = $3, kategori = $4 WHERE id = $5`
	_, err := r.db.Exec(query, p.Judul, p.Isi, p.Tanggal, p.Kategori, p.ID)
	return err
}

// Delete menghapus pengumuman
func (r *pengumumanRepo) Delete(id int) error {
	query := `DELETE FROM pengumuman WHERE id = $1`
	_, err := r.db.Exec(query, id)
	return err
}

// MarkAsRead menandai pengumuman sebagai sudah dibaca
func (r *pengumumanRepo) MarkAsRead(id int) error {
	query := `UPDATE pengumuman SET is_read = true WHERE id = $1`
	_, err := r.db.Exec(query, id)
	return err
}
