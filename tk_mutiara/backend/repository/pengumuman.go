package repository

import (
	"database/sql"
	"log"
	"tk_mutiara_backend/models"
)

// GetAllPengumuman mengambil semua pengumuman dari database
func GetAllPengumuman(db *sql.DB) ([]models.Pengumuman, error) {
	query := `
		SELECT 
			p.id_pengumuman,
			p.id_guru,
			COALESCE(g.nama_guru, '') as nama_guru,
			p.judul,
			COALESCE(p.media, '') as media,
			DATE_FORMAT(p.waktu_unggah, '%Y-%m-%d %H:%i:%s') as waktu_unggah,
			p.deskripsi,
			DATE_FORMAT(p.created_at, '%Y-%m-%d %H:%i:%s') as created_at,
			DATE_FORMAT(p.updated_at, '%Y-%m-%d %H:%i:%s') as updated_at
		FROM pengumuman p
		LEFT JOIN guru g ON p.id_guru = g.id_guru
		ORDER BY p.waktu_unggah DESC
	`

	rows, err := db.Query(query)
	if err != nil {
		log.Printf("Error querying pengumuman: %v", err)
		return nil, err
	}
	defer rows.Close()

	var pengumuman []models.Pengumuman

	for rows.Next() {
		var p models.Pengumuman
		err := rows.Scan(
			&p.IDPengumuman,
			&p.IDGuru,
			&p.NamaGuru,
			&p.Judul,
			&p.Media,
			&p.WaktuUnggah,
			&p.Deskripsi,
			&p.CreatedAt,
			&p.UpdatedAt,
		)
		if err != nil {
			log.Printf("Error scanning pengumuman row: %v", err)
			return nil, err
		}
		pengumuman = append(pengumuman, p)
	}

	if err = rows.Err(); err != nil {
		log.Printf("Error iterating pengumuman rows: %v", err)
		return nil, err
	}

	return pengumuman, nil
}

// GetPengumumanByID mengambil pengumuman berdasarkan ID
func GetPengumumanByID(db *sql.DB, id int64) (models.Pengumuman, error) {
	query := `
		SELECT 
			p.id_pengumuman,
			p.id_guru,
			COALESCE(g.nama_guru, '') as nama_guru,
			p.judul,
			COALESCE(p.media, '') as media,
			DATE_FORMAT(p.waktu_unggah, '%Y-%m-%d %H:%i:%s') as waktu_unggah,
			p.deskripsi,
			DATE_FORMAT(p.created_at, '%Y-%m-%d %H:%i:%s') as created_at,
			DATE_FORMAT(p.updated_at, '%Y-%m-%d %H:%i:%s') as updated_at
		FROM pengumuman p
		LEFT JOIN guru g ON p.id_guru = g.id_guru
		WHERE p.id_pengumuman = ?
	`

	var p models.Pengumuman
	err := db.QueryRow(query, id).Scan(
		&p.IDPengumuman,
		&p.IDGuru,
		&p.NamaGuru,
		&p.Judul,
		&p.Media,
		&p.WaktuUnggah,
		&p.Deskripsi,
		&p.CreatedAt,
		&p.UpdatedAt,
	)

	if err != nil {
		if err == sql.ErrNoRows {
			log.Printf("Pengumuman not found: %d", id)
			return p, err
		}
		log.Printf("Error querying pengumuman by ID: %v", err)
		return p, err
	}

	return p, nil
}
