package handlers

import (
	"net/http"
	"strconv"

	"github.com/gin-gonic/gin"
	"tk_mutiara_backend/config"
	"tk_mutiara_backend/models"
	"tk_mutiara_backend/services"
)

// ==============================
// DASHBOARD HANDLERS
// ==============================

// GetDashboardMetrics handler untuk GET /api/admin/dashboard/metrics
func GetDashboardMetrics(c *gin.Context) {
	metrics, err := services.GetDashboardOverview(config.DB)
	if err != nil {
		c.JSON(http.StatusInternalServerError, models.ApiResponse{
			Success: false,
			Error:   err.Error(),
		})
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Dashboard metrics berhasil diambil",
		Data:    metrics,
	})
}

// GetDashboardStatistics handler untuk GET /api/admin/dashboard/statistics
func GetDashboardStatistics(c *gin.Context) {
	limitStr := c.DefaultQuery("limit", "30")
	limit, err := strconv.Atoi(limitStr)
	if err != nil {
		limit = 30
	}

	statistics, err := services.GetDashboardStatistics(config.DB, limit)
	if err != nil {
		c.JSON(http.StatusInternalServerError, models.ApiResponse{
			Success: false,
			Error:   err.Error(),
		})
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Dashboard statistics berhasil diambil",
		Data:    statistics,
	})
}

// ==============================
// GURU HANDLERS
// ==============================

// GetAllGuru handler untuk GET /api/admin/guru
func GetAllGuru(c *gin.Context) {
	gurus, err := services.GetAllGurus(config.DB)
	if err != nil {
		c.JSON(http.StatusInternalServerError, models.ApiResponse{
			Success: false,
			Error:   err.Error(),
		})
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Data guru berhasil diambil",
		Data:    gurus,
	})
}

// GetGuruByID handler untuk GET /api/admin/guru/:id
func GetGuruByID(c *gin.Context) {
	idStr := c.Param("id")
	id, err := strconv.Atoi(idStr)
	if err != nil {
		c.JSON(http.StatusBadRequest, models.ApiResponse{
			Success: false,
			Error:   "ID guru tidak valid",
		})
		return
	}

	guru, err := services.GetGuruDetail(config.DB, id)
	if err != nil {
		c.JSON(http.StatusNotFound, models.ApiResponse{
			Success: false,
			Error:   "Guru tidak ditemukan",
		})
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Data guru berhasil diambil",
		Data:    guru,
	})
}

// CreateGuru handler untuk POST /api/admin/guru
func CreateGuru(c *gin.Context) {
	var req models.CreateGuruRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, models.ApiResponse{
			Success: false,
			Error:   err.Error(),
		})
		return
	}

	id, err := services.CreateNewGuru(config.DB, &req)
	if err != nil {
		c.JSON(http.StatusBadRequest, models.ApiResponse{
			Success: false,
			Error:   err.Error(),
		})
		return
	}

	c.JSON(http.StatusCreated, models.ApiResponse{
		Success: true,
		Message: "Guru berhasil dibuat",
		Data: map[string]interface{}{
			"id_guru": id,
		},
	})
}

// UpdateGuru handler untuk PUT /api/admin/guru/:id
func UpdateGuru(c *gin.Context) {
	idStr := c.Param("id")
	id, err := strconv.Atoi(idStr)
	if err != nil {
		c.JSON(http.StatusBadRequest, models.ApiResponse{
			Success: false,
			Error:   "ID guru tidak valid",
		})
		return
	}

	var req models.UpdateGuruRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, models.ApiResponse{
			Success: false,
			Error:   err.Error(),
		})
		return
	}

	err = services.UpdateGuruData(config.DB, id, &req)
	if err != nil {
		c.JSON(http.StatusBadRequest, models.ApiResponse{
			Success: false,
			Error:   err.Error(),
		})
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Guru berhasil diupdate",
	})
}

// DeleteGuru handler untuk DELETE /api/admin/guru/:id
func DeleteGuru(c *gin.Context) {
	idStr := c.Param("id")
	id, err := strconv.Atoi(idStr)
	if err != nil {
		c.JSON(http.StatusBadRequest, models.ApiResponse{
			Success: false,
			Error:   "ID guru tidak valid",
		})
		return
	}

	err = services.RemoveGuru(config.DB, id)
	if err != nil {
		c.JSON(http.StatusBadRequest, models.ApiResponse{
			Success: false,
			Error:   err.Error(),
		})
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Guru berhasil dihapus",
	})
}

// ==============================
// KELAS HANDLERS
// ==============================

// GetAllKelas handler untuk GET /api/admin/kelas
func GetAllKelas(c *gin.Context) {
	kelas, err := services.GetAllKelas(config.DB)
	if err != nil {
		c.JSON(http.StatusInternalServerError, models.ApiResponse{
			Success: false,
			Error:   err.Error(),
		})
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Data kelas berhasil diambil",
		Data:    kelas,
	})
}

// GetKelasByID handler untuk GET /api/admin/kelas/:id
func GetKelasByID(c *gin.Context) {
	idStr := c.Param("id")
	id, err := strconv.Atoi(idStr)
	if err != nil {
		c.JSON(http.StatusBadRequest, models.ApiResponse{
			Success: false,
			Error:   "ID kelas tidak valid",
		})
		return
	}

	kelas, err := services.GetKelasDetail(config.DB, id)
	if err != nil {
		c.JSON(http.StatusNotFound, models.ApiResponse{
			Success: false,
			Error:   "Kelas tidak ditemukan",
		})
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Data kelas berhasil diambil",
		Data:    kelas,
	})
}

// CreateKelas handler untuk POST /api/admin/kelas
func CreateKelas(c *gin.Context) {
	var req models.CreateKelasRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, models.ApiResponse{
			Success: false,
			Error:   err.Error(),
		})
		return
	}

	id, err := services.CreateNewKelas(config.DB, &req)
	if err != nil {
		c.JSON(http.StatusBadRequest, models.ApiResponse{
			Success: false,
			Error:   err.Error(),
		})
		return
	}

	c.JSON(http.StatusCreated, models.ApiResponse{
		Success: true,
		Message: "Kelas berhasil dibuat",
		Data: map[string]interface{}{
			"id_kelas": id,
		},
	})
}

// DeleteKelas handler untuk DELETE /api/admin/kelas/:id
func DeleteKelas(c *gin.Context) {
	idStr := c.Param("id")
	id, err := strconv.Atoi(idStr)
	if err != nil {
		c.JSON(http.StatusBadRequest, models.ApiResponse{
			Success: false,
			Error:   "ID kelas tidak valid",
		})
		return
	}

	err = services.RemoveKelas(config.DB, id)
	if err != nil {
		c.JSON(http.StatusBadRequest, models.ApiResponse{
			Success: false,
			Error:   err.Error(),
		})
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Kelas berhasil dihapus",
	})
}
