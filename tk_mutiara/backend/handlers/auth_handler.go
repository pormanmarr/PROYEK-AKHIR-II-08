package handlers

import (
	"database/sql"
	"fmt"
	"net/http"
	"time"

	"tk_mutiara_backend/config"
	"tk_mutiara_backend/models"

	"github.com/gin-gonic/gin"
	"github.com/golang-jwt/jwt/v5"
	"golang.org/x/crypto/bcrypt"
)

// LoginHandler handles user login
func LoginHandler(c *gin.Context) {
	var req struct {
		Email    string `json:"email" binding:"required"`
		Password string `json:"password" binding:"required"`
	}

	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, models.ApiResponse{
			Success: false,
			Error:   "Email dan password harus diisi",
		})
		return
	}

	// Query database untuk cek user
	var userID int
	var userName string
	var userRole string
	var password string
	var nomorIndukSiswa sql.NullString
	var idGuru sql.NullInt64

	query := `
		SELECT a.id_akun, a.username, a.role, a.password, a.nomor_induk_siswa, a.id_guru
		FROM akun a
		WHERE a.username = ?
	`

	err := config.DB.QueryRow(query, req.Email).Scan(&userID, &userName, &userRole, &password, &nomorIndukSiswa, &idGuru)
	if err != nil {
		if err == sql.ErrNoRows {
			c.JSON(http.StatusUnauthorized, models.ApiResponse{
				Success: false,
				Error:   "Email atau password salah",
			})
			return
		}
		c.JSON(http.StatusInternalServerError, models.ApiResponse{
			Success: false,
			Error:   "Terjadi kesalahan server",
		})
		return
	}

	// Verify password menggunakan bcrypt
	err = bcrypt.CompareHashAndPassword([]byte(password), []byte(req.Password))
	if err != nil {
		c.JSON(http.StatusUnauthorized, models.ApiResponse{
			Success: false,
			Error:   "Email atau password salah",
		})
		return
	}

	// Generate JWT token
	token := jwt.NewWithClaims(jwt.SigningMethodHS256, jwt.MapClaims{
		"user_id":           userID,
		"username":          userName,
		"role":              userRole,
		"nomor_induk_siswa": nomorIndukSiswa.String,
		"id_guru":           idGuru.Int64,
		"exp":               time.Now().Add(time.Hour * 24).Unix(),
	})

	tokenString, err := token.SignedString([]byte(config.AppConfig.JWTSecret))
	if err != nil {
		c.JSON(http.StatusInternalServerError, models.ApiResponse{
			Success: false,
			Error:   "Gagal membuat token",
		})
		return
	}

	// Get user details
	var userData models.UserLogin
	if userRole == "orangtua" {
		// Get siswa data
		queryChild := `
			SELECT s.nomor_induk_siswa, s.nama_siswa, s.nama_orgtua, k.nama_kelas
			FROM siswa s
			JOIN kelas k ON s.id_kelas = k.id_kelas
			WHERE s.nomor_induk_siswa = ?
		`
		err := config.DB.QueryRow(queryChild, nomorIndukSiswa.String).Scan(
			&userData.NomorIndukSiswa, &userData.NamaSiswa, &userData.NamaOrtu, &userData.Kelas,
		)
		if err != nil {
			fmt.Println("Error fetching child data:", err)
		}

	} else if userRole == "guru" {
		// Get guru data
		queryGuru := `
			SELECT id_guru, nama_guru, email
			FROM guru
			WHERE id_guru = ?
		`
		var email string
		err := config.DB.QueryRow(queryGuru, idGuru.Int64).Scan(
			&userData.IDGuru, &userData.NamaGuru, &email,
		)
		if err != nil {
			fmt.Println("Error fetching guru data:", err)
		}
	}

	c.JSON(http.StatusOK, gin.H{
		"success": true,
		"token":   tokenString,
		"user": gin.H{
			"id":                userID,
			"username":          userName,
			"role":              userRole,
			"nomor_induk_siswa": nomorIndukSiswa.String,
			"nama_siswa":        userData.NamaSiswa,
			"nama_ortu":         userData.NamaOrtu,
			"kelas":             userData.Kelas,
			"id_guru":           idGuru.Int64,
			"nama_guru":         userData.NamaGuru,
		},
	})
}
