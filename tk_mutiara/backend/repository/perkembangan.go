package repository

import (
	"database/sql"
	"errors"
	"tk_mutiara_backend/config"
	"tk_mutiara_backend/models"
)

// PerkembanganRepository interface untuk perkembangan operations
type PerkembanganRepository interface {
	GetAll() ([]models.Perkembangan, error)
	GetByID(id int) (*models.Perkembangan, error)
	GetByNamaAnak(namaAnak string) ([]models.Perkembangan, error)
	Create(p *models.Perkembangan) error
	Update(p *models.Perkembangan) error
	Delete(id int) error
}

// perkembanganRepo implementasi repository
type perkembanganRepo struct {
	db *sql.DB
}

// NewPerkembanganRepository membuat instance baru
func NewPerkembanganRepository() PerkembanganRepository {
	return &perkembanganRepo{db: config.DB}
}

// GetAll mengambil semua perkembangan
func (r *perkembanganRepo) GetAll() ([]models.Perkembangan, error) {
	query := `SELECT id, nama_anak, tanggal, kategori, deskripsi, nilai_kognitif, nilai_motorik, 
	          nilai_sosial, nilai_bahasa, nilai_seni, catatan FROM perkembangan ORDER BY tanggal DESC`
	
	rows, err := r.db.Query(query)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var perkembangan []models.Perkembangan
	for rows.Next() {
		var p models.Perkembangan
		if err := rows.Scan(&p.ID, &p.NamaAnak, &p.Tanggal, &p.Kategori, &p.Deskripsi,
			&p.NilaiKognitif, &p.NilaiMotorik, &p.NilaiSosial, &p.NilaiBahasa, &p.NilaiSeni, &p.Catatan); err != nil {
			return nil, err
		}
		perkembangan = append(perkembangan, p)
	}

	return perkembangan, nil
}

// GetByID mengambil perkembangan berdasarkan ID
func (r *perkembanganRepo) GetByID(id int) (*models.Perkembangan, error) {
	query := `SELECT id, nama_anak, tanggal, kategori, deskripsi, nilai_kognitif, nilai_motorik, 
	          nilai_sosial, nilai_bahasa, nilai_seni, catatan FROM perkembangan WHERE id = $1`
	
	var p models.Perkembangan
	err := r.db.QueryRow(query, id).Scan(&p.ID, &p.NamaAnak, &p.Tanggal, &p.Kategori, &p.Deskripsi,
		&p.NilaiKognitif, &p.NilaiMotorik, &p.NilaiSosial, &p.NilaiBahasa, &p.NilaiSeni, &p.Catatan)
	if err != nil {
		if err == sql.ErrNoRows {
			return nil, errors.New("perkembangan tidak ditemukan")
		}
		return nil, err
	}

	return &p, nil
}

// GetByNamaAnak mengambil perkembangan berdasarkan nama anak
func (r *perkembanganRepo) GetByNamaAnak(namaAnak string) ([]models.Perkembangan, error) {
	query := `SELECT id, nama_anak, tanggal, kategori, deskripsi, nilai_kognitif, nilai_motorik, 
	          nilai_sosial, nilai_bahasa, nilai_seni, catatan FROM perkembangan WHERE nama_anak = $1 ORDER BY tanggal DESC`
	
	rows, err := r.db.Query(query, namaAnak)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var perkembangan []models.Perkembangan
	for rows.Next() {
		var p models.Perkembangan
		if err := rows.Scan(&p.ID, &p.NamaAnak, &p.Tanggal, &p.Kategori, &p.Deskripsi,
			&p.NilaiKognitif, &p.NilaiMotorik, &p.NilaiSosial, &p.NilaiBahasa, &p.NilaiSeni, &p.Catatan); err != nil {
			return nil, err
		}
		perkembangan = append(perkembangan, p)
	}

	return perkembangan, nil
}

// Create membuat perkembangan baru
func (r *perkembanganRepo) Create(p *models.Perkembangan) error {
	query := `INSERT INTO perkembangan (nama_anak, tanggal, kategori, deskripsi, nilai_kognitif, nilai_motorik, 
	          nilai_sosial, nilai_bahasa, nilai_seni, catatan) 
	          VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10) RETURNING id`
	err := r.db.QueryRow(query, p.NamaAnak, p.Tanggal, p.Kategori, p.Deskripsi, p.NilaiKognitif,
		p.NilaiMotorik, p.NilaiSosial, p.NilaiBahasa, p.NilaiSeni, p.Catatan).Scan(&p.ID)
	return err
}

// Update mengupdate perkembangan
func (r *perkembanganRepo) Update(p *models.Perkembangan) error {
	query := `UPDATE perkembangan SET nama_anak = $1, tanggal = $2, kategori = $3, deskripsi = $4, 
	          nilai_kognitif = $5, nilai_motorik = $6, nilai_sosial = $7, nilai_bahasa = $8, nilai_seni = $9, 
	          catatan = $10 WHERE id = $11`
	_, err := r.db.Exec(query, p.NamaAnak, p.Tanggal, p.Kategori, p.Deskripsi, p.NilaiKognitif,
		p.NilaiMotorik, p.NilaiSosial, p.NilaiBahasa, p.NilaiSeni, p.Catatan, p.ID)
	return err
}

// Delete menghapus perkembangan
func (r *perkembanganRepo) Delete(id int) error {
	query := `DELETE FROM perkembangan WHERE id = $1`
	_, err := r.db.Exec(query, id)
	return err
}
