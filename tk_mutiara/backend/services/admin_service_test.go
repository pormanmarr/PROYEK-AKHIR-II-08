package services

import (
	"database/sql"
	"testing"
	"tk_mutiara_backend/models"
)

// Test GetDashboardOverview
func TestGetDashboardOverview(t *testing.T) {
	// Setup: create test database connection
	// db := setupTestDB()
	// defer db.Close()

	// Run
	// metrics, err := GetDashboardOverview(db)

	// Assert
	// if err != nil {
	//     t.Errorf("Error getting dashboard overview: %v", err)
	// }

	// if metrics.TotalSiswa < 0 {
	//     t.Errorf("Expected positive total siswa, got %d", metrics.TotalSiswa)
	// }
}

// Test CreateNewGuru - Valid Input
func TestCreateNewGuru_ValidInput(t *testing.T) {
	// Setup
	// db := setupTestDB()
	// defer db.Close()
	_ = &models.CreateGuruRequest{
		NamaGuru: "Ibu Siti",
		NoHP:     "081234567890",
		Email:    "siti@test.com",
	}

	// Run
	// id, err := CreateNewGuru(db, guruReq)

	// Assert
	// if err != nil {
	//     t.Errorf("Error creating guru: %v", err)
	// }

	// if id <= 0 {
	//     t.Errorf("Expected positive ID, got %d", id)
	// }
}

// Test CreateNewGuru - Invalid Input (empty name)
func TestCreateNewGuru_InvalidInput(t *testing.T) {
	// Setup
	// db := setupTestDB()
	// defer db.Close()
	_ = &models.CreateGuruRequest{
		NamaGuru: "", // Invalid - empty
		NoHP:     "081234567890",
		Email:    "siti@test.com",
	}

	// Run
	// id, err := CreateNewGuru(db, guruReq)

	// Assert
	// if err == nil {
	//     t.Errorf("Expected error for empty guru name, got nil")
	// }

	// if id > 0 {
	//     t.Errorf("Expected no ID for invalid input, got %d", id)
	// }
}

// Test GetAllSiswa
func TestGetAllSiswa(t *testing.T) {
	// Setup
	// db := setupTestDB()
	// defer db.Close()

	// Run
	// siswa, err := GetAllSiswa(db)

	// Assert
	// if err != nil {
	//     t.Errorf("Error getting siswa: %v", err)
	// }

	// if siswa == nil {
	//     t.Errorf("Expected siswa list, got nil")
	// }
}

// Helper function untuk setup test database
func setupTestDB() *sql.DB {
	// Setup test database connection
	// return db connection untuk testing
	return nil
}
