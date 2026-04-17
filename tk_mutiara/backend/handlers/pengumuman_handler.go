package handlers

import (
	"log"
	"net/http"
	"strconv"

	"tk_mutiara_backend/config"
	"tk_mutiara_backend/models"
	"tk_mutiara_backend/repository"

	"github.com/gin-gonic/gin"
)

// GetPengumumanHandler mengambil semua pengumuman
func GetPengumumanHandler(c *gin.Context) {
	log.Println("=== GET /api/pengumuman ===")

	pengumuman, err := repository.GetAllPengumuman(config.DB)
	if err != nil {
		log.Printf("Error getting pengumuman: %v", err)
		c.JSON(http.StatusInternalServerError, models.ApiResponse{
			Success: false,
			Message: "Gagal mengambil data pengumuman",
			Error:   err.Error(),
		})
		return
	}

	log.Printf("Success: Retrieved %d pengumuman records", len(pengumuman))
	c.JSON(http.StatusOK, pengumuman)
}

// GetPengumumanByIDHandler mengambil pengumuman berdasarkan ID
func GetPengumumanByIDHandler(c *gin.Context) {
	idStr := c.Param("id")
	log.Printf("=== GET /api/pengumuman/:id (id=%s) ===", idStr)

	id, err := strconv.ParseInt(idStr, 10, 64)
	if err != nil {
		log.Printf("Error parsing ID: %v", err)
		c.JSON(http.StatusBadRequest, models.ApiResponse{
			Success: false,
			Message: "ID pengumuman tidak valid",
			Error:   err.Error(),
		})
		return
	}

	pengumuman, err := repository.GetPengumumanByID(config.DB, id)
	if err != nil {
		log.Printf("Error getting pengumuman by ID: %v", err)
		c.JSON(http.StatusNotFound, models.ApiResponse{
			Success: false,
			Message: "Pengumuman tidak ditemukan",
			Error:   err.Error(),
		})
		return
	}

	log.Printf("Success: Retrieved pengumuman with ID %d", id)
	c.JSON(http.StatusOK, pengumuman)
}
