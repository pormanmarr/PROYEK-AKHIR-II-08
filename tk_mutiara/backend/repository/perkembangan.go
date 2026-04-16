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
	GetByNomorInduk(nomorInduk string) ([]models.Perkembangan, error)
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
	query := `SELECT p.id_perkembangan, p.id_guru, p.nomor_induk_siswa, s.nama_siswa, 
	          COALESCE(g.nama_guru, '') as nama_guru,
	          COALESCE(k.nama_kelas, '') as kelas,
	          COALESCE(p.bulan, 0) as bulan, COALESCE(p.tahun, 0) as tahun, 
	          COALESCE(p.kategori, '') as kategori,
	          COALESCE(p.deskripsi, '') as deskripsi,
	          COALESCE(p.template_deskripsi, '') as template_deskripsi,
	          COALESCE(p.status_utama, 'BSH') as status_utama,
	          DATE_FORMAT(p.created_at, '%Y-%m-%d %H:%i:%s') as created_at,
	          DATE_FORMAT(p.updated_at, '%Y-%m-%d %H:%i:%s') as updated_at
	          FROM perkembangan p
	          INNER JOIN siswa s ON p.nomor_induk_siswa = s.nomor_induk_siswa
	          LEFT JOIN guru g ON p.id_guru = g.id_guru
	          LEFT JOIN kelas k ON s.id_kelas = k.id_kelas
	          ORDER BY p.id_perkembangan DESC`

	rows, err := r.db.Query(query)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var perkembangan []models.Perkembangan
	for rows.Next() {
		var p models.Perkembangan
		if err := rows.Scan(&p.IDPerkembangan, &p.IDGuru, &p.NomorIndukSiswa, &p.NamaAnak, &p.NamaGuru,
			&p.Kelas, &p.Bulan, &p.Tahun, &p.Kategori, &p.Deskripsi, &p.TemplateDeskripsi, &p.StatusUtama, &p.CreatedAt, &p.UpdatedAt); err != nil {
			return nil, err
		}
		perkembangan = append(perkembangan, p)
	}

	return perkembangan, nil
}

// GetByID mengambil perkembangan berdasarkan ID
func (r *perkembanganRepo) GetByID(id int) (*models.Perkembangan, error) {
	query := `SELECT p.id_perkembangan, p.id_guru, p.nomor_induk_siswa, s.nama_siswa, 
	          COALESCE(g.nama_guru, '') as nama_guru,
	          COALESCE(k.nama_kelas, '') as kelas,
	          COALESCE(p.bulan, 0) as bulan, COALESCE(p.tahun, 0) as tahun,
	          COALESCE(p.kategori, '') as kategori,
	          COALESCE(p.deskripsi, '') as deskripsi,
	          COALESCE(p.template_deskripsi, '') as template_deskripsi,
	          COALESCE(p.status_utama, 'BSH') as status_utama,
	          DATE_FORMAT(p.created_at, '%Y-%m-%d %H:%i:%s') as created_at,
	          DATE_FORMAT(p.updated_at, '%Y-%m-%d %H:%i:%s') as updated_at
	          FROM perkembangan p
	          INNER JOIN siswa s ON p.nomor_induk_siswa = s.nomor_induk_siswa
	          LEFT JOIN guru g ON p.id_guru = g.id_guru
	          LEFT JOIN kelas k ON s.id_kelas = k.id_kelas
	          WHERE p.id_perkembangan = ?`

	var p models.Perkembangan
	err := r.db.QueryRow(query, id).Scan(&p.IDPerkembangan, &p.IDGuru, &p.NomorIndukSiswa, &p.NamaAnak, &p.NamaGuru,
		&p.Kelas, &p.Bulan, &p.Tahun, &p.Kategori, &p.Deskripsi, &p.TemplateDeskripsi, &p.StatusUtama, &p.CreatedAt, &p.UpdatedAt)
	if err != nil {
		if err == sql.ErrNoRows {
			return nil, errors.New("perkembangan tidak ditemukan")
		}
		return nil, err
	}

	return &p, nil
}

// GetByNomorInduk mengambil perkembangan berdasarkan nomor induk siswa
func (r *perkembanganRepo) GetByNomorInduk(nomorInduk string) ([]models.Perkembangan, error) {
	query := `SELECT p.id_perkembangan, p.id_guru, p.nomor_induk_siswa, s.nama_siswa, 
	          COALESCE(g.nama_guru, '') as nama_guru,
	          COALESCE(k.nama_kelas, '') as kelas,
	          COALESCE(p.bulan, 0) as bulan, COALESCE(p.tahun, 0) as tahun,
	          COALESCE(p.kategori, '') as kategori,
	          COALESCE(p.deskripsi, '') as deskripsi,
	          COALESCE(p.template_deskripsi, '') as template_deskripsi,
	          COALESCE(p.status_utama, 'BSH') as status_utama,
	          DATE_FORMAT(p.created_at, '%Y-%m-%d %H:%i:%s') as created_at,
	          DATE_FORMAT(p.updated_at, '%Y-%m-%d %H:%i:%s') as updated_at
	          FROM perkembangan p
	          INNER JOIN siswa s ON p.nomor_induk_siswa = s.nomor_induk_siswa
	          LEFT JOIN guru g ON p.id_guru = g.id_guru
	          LEFT JOIN kelas k ON s.id_kelas = k.id_kelas
	          WHERE p.nomor_induk_siswa = ? ORDER BY p.id_perkembangan DESC`

	rows, err := r.db.Query(query, nomorInduk)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var perkembangan []models.Perkembangan
	for rows.Next() {
		var p models.Perkembangan
		if err := rows.Scan(&p.IDPerkembangan, &p.IDGuru, &p.NomorIndukSiswa, &p.NamaAnak, &p.NamaGuru,
			&p.Kelas, &p.Bulan, &p.Tahun, &p.Kategori, &p.Deskripsi, &p.TemplateDeskripsi, &p.StatusUtama, &p.CreatedAt, &p.UpdatedAt); err != nil {
			return nil, err
		}
		perkembangan = append(perkembangan, p)
	}

	return perkembangan, nil
}

// Create membuat perkembangan baru
func (r *perkembanganRepo) Create(p *models.Perkembangan) error {
	query := `INSERT INTO perkembangan (id_guru, nomor_induk_siswa, bulan, tahun, kategori, deskripsi, status_utama) 
	          VALUES (?, ?, ?, ?, ?, ?, ?) RETURNING id_perkembangan`
	err := r.db.QueryRow(query, p.IDGuru, p.NomorIndukSiswa, p.Bulan, p.Tahun, p.Kategori, p.Deskripsi, p.StatusUtama).Scan(&p.IDPerkembangan)
	return err
}

// Update mengupdate perkembangan
func (r *perkembanganRepo) Update(p *models.Perkembangan) error {
	query := `UPDATE perkembangan SET status_utama = ?, bulan = ?, tahun = ?, kategori = ?, deskripsi = ? 
	          WHERE id_perkembangan = ?`
	_, err := r.db.Exec(query, p.StatusUtama, p.Bulan, p.Tahun, p.Kategori, p.Deskripsi, p.IDPerkembangan)
	return err
}

// Delete menghapus perkembangan
func (r *perkembanganRepo) Delete(id int) error {
	query := `DELETE FROM perkembangan WHERE id_perkembangan = ?`
	_, err := r.db.Exec(query, id)
	return err
}
