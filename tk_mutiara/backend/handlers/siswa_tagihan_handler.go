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
// SISWA HANDLERS
// ==============================

// GetAllSiswa handler untuk GET /api/admin/siswa
func GetAllSiswa(c *gin.Context) {
	siswa, err := services.GetAllSiswa(config.DB)
	if err != nil {
		c.JSON(http.StatusInternalServerError, models.ApiResponse{
			Success: false,
			Error:   err.Error(),
		})
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Data siswa berhasil diambil",
		Data:    siswa,
	})
}

// GetSiswaByID handler untuk GET /api/admin/siswa/:id
func GetSiswaByID(c *gin.Context) {
	nomorIndukSiswa := c.Param("id")
	if nomorIndukSiswa == "" {
		c.JSON(http.StatusBadRequest, models.ApiResponse{
			Success: false,
			Error:   "Nomor induk siswa tidak valid",
		})
		return
	}

	siswa, err := services.GetSiswaDetail(config.DB, nomorIndukSiswa)
	if err != nil {
		c.JSON(http.StatusNotFound, models.ApiResponse{
			Success: false,
			Error:   "Siswa tidak ditemukan",
		})
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Data siswa berhasil diambil",
		Data:    siswa,
	})
}

// GetSiswaByKelas handler untuk GET /api/admin/kelas/:id/siswa
func GetSiswaByKelas(c *gin.Context) {
	idStr := c.Param("id")
	id, err := strconv.Atoi(idStr)
	if err != nil {
		c.JSON(http.StatusBadRequest, models.ApiResponse{
			Success: false,
			Error:   "ID kelas tidak valid",
		})
		return
	}

	siswa, err := services.GetSiswaByKelas(config.DB, id)
	if err != nil {
		c.JSON(http.StatusInternalServerError, models.ApiResponse{
			Success: false,
			Error:   err.Error(),
		})
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Data siswa berhasil diambil",
		Data:    siswa,
	})
}

// CreateSiswa handler untuk POST /api/admin/siswa
func CreateSiswa(c *gin.Context) {
	var req models.CreateSiswaRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, models.ApiResponse{
			Success: false,
			Error:   err.Error(),
		})
		return
	}

	id, err := services.CreateNewSiswa(config.DB, &req)
	if err != nil {
		c.JSON(http.StatusBadRequest, models.ApiResponse{
			Success: false,
			Error:   err.Error(),
		})
		return
	}

	c.JSON(http.StatusCreated, models.ApiResponse{
		Success: true,
		Message: "Siswa berhasil dibuat",
		Data: map[string]interface{}{
			"nomor_induk_siswa": id,
		},
	})
}

// DeleteSiswa handler untuk DELETE /api/admin/siswa/:id
func DeleteSiswa(c *gin.Context) {
	nomorIndukSiswa := c.Param("id")
	if nomorIndukSiswa == "" {
		c.JSON(http.StatusBadRequest, models.ApiResponse{
			Success: false,
			Error:   "Nomor induk siswa tidak valid",
		})
		return
	}

	err := services.RemoveSiswa(config.DB, nomorIndukSiswa)
	if err != nil {
		c.JSON(http.StatusBadRequest, models.ApiResponse{
			Success: false,
			Error:   err.Error(),
		})
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Siswa berhasil dihapus",
	})
}

// ==============================
// TAGIHAN HANDLERS
// ==============================

// GetAllTagihan handler untuk GET /api/admin/tagihan
func GetAllTagihan(c *gin.Context) {
	tagihan, err := services.GetAllTagihan(config.DB)
	if err != nil {
		c.JSON(http.StatusInternalServerError, models.ApiResponse{
			Success: false,
			Error:   err.Error(),
		})
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Data tagihan berhasil diambil",
		Data:    tagihan,
	})
}

// GetTagihanByID handler untuk GET /api/admin/tagihan/:id
func GetTagihanByID(c *gin.Context) {
	idStr := c.Param("id")
	id, err := strconv.Atoi(idStr)
	if err != nil {
		c.JSON(http.StatusBadRequest, models.ApiResponse{
			Success: false,
			Error:   "ID tagihan tidak valid",
		})
		return
	}

	tagihan, err := services.GetTagihanDetail(config.DB, id)
	if err != nil {
		c.JSON(http.StatusNotFound, models.ApiResponse{
			Success: false,
			Error:   "Tagihan tidak ditemukan",
		})
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Data tagihan berhasil diambil",
		Data:    tagihan,
	})
}

// GetTagihanBySiswa handler untuk GET /api/admin/siswa/:id/tagihan
func GetTagihanBySiswa(c *gin.Context) {
	nomorIndukSiswa := c.Param("id")
	if nomorIndukSiswa == "" {
		c.JSON(http.StatusBadRequest, models.ApiResponse{
			Success: false,
			Error:   "Nomor induk siswa tidak valid",
		})
		return
	}

	tagihan, err := services.GetTagihanBySiswa(config.DB, nomorIndukSiswa)
	if err != nil {
		c.JSON(http.StatusInternalServerError, models.ApiResponse{
			Success: false,
			Error:   err.Error(),
		})
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Data tagihan berhasil diambil",
		Data:    tagihan,
	})
}

// CreateTagihan handler untuk POST /api/admin/tagihan
func CreateTagihan(c *gin.Context) {
	var req models.CreateTagihanRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, models.ApiResponse{
			Success: false,
			Error:   err.Error(),
		})
		return
	}

	id, err := services.CreateNewTagihan(config.DB, &req)
	if err != nil {
		c.JSON(http.StatusBadRequest, models.ApiResponse{
			Success: false,
			Error:   err.Error(),
		})
		return
	}

	c.JSON(http.StatusCreated, models.ApiResponse{
		Success: true,
		Message: "Tagihan berhasil dibuat",
		Data: map[string]interface{}{
			"id_tagihan": id,
		},
	})
}

// DeleteTagihan handler untuk DELETE /api/admin/tagihan/:id
func DeleteTagihan(c *gin.Context) {
	idStr := c.Param("id")
	id, err := strconv.Atoi(idStr)
	if err != nil {
		c.JSON(http.StatusBadRequest, models.ApiResponse{
			Success: false,
			Error:   "ID tagihan tidak valid",
		})
		return
	}

	err = services.RemoveTagihan(config.DB, id)
	if err != nil {
		c.JSON(http.StatusBadRequest, models.ApiResponse{
			Success: false,
			Error:   err.Error(),
		})
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Tagihan berhasil dihapus",
	})
}

// ==============================
// PEMBAYARAN HANDLERS
// ==============================

// GetAllPembayaran handler untuk GET /api/admin/pembayaran
func GetAllPembayaran(c *gin.Context) {
	pembayaran, err := services.GetAllPembayaran(config.DB)
	if err != nil {
		c.JSON(http.StatusInternalServerError, models.ApiResponse{
			Success: false,
			Error:   err.Error(),
		})
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Data pembayaran berhasil diambil",
		Data:    pembayaran,
	})
}

// GetPembayaranByID handler untuk GET /api/admin/pembayaran/:id
func GetPembayaranByID(c *gin.Context) {
	idStr := c.Param("id")
	id, err := strconv.Atoi(idStr)
	if err != nil {
		c.JSON(http.StatusBadRequest, models.ApiResponse{
			Success: false,
			Error:   "ID pembayaran tidak valid",
		})
		return
	}

	pembayaran, err := services.GetPembayaranDetail(config.DB, id)
	if err != nil {
		c.JSON(http.StatusNotFound, models.ApiResponse{
			Success: false,
			Error:   "Pembayaran tidak ditemukan",
		})
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Data pembayaran berhasil diambil",
		Data:    pembayaran,
	})
}

// GetPembayaranByTagihan handler untuk GET /api/admin/tagihan/:id/pembayaran
func GetPembayaranByTagihan(c *gin.Context) {
	idStr := c.Param("id")
	id, err := strconv.Atoi(idStr)
	if err != nil {
		c.JSON(http.StatusBadRequest, models.ApiResponse{
			Success: false,
			Error:   "ID tagihan tidak valid",
		})
		return
	}

	pembayaran, err := services.GetPembayaranByTagihan(config.DB, id)
	if err != nil {
		c.JSON(http.StatusInternalServerError, models.ApiResponse{
			Success: false,
			Error:   err.Error(),
		})
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Data pembayaran berhasil diambil",
		Data:    pembayaran,
	})
}

// UpdatePembayaranStatus handler untuk PUT /api/admin/pembayaran/:id/status
func UpdatePembayaranStatus(c *gin.Context) {
	idStr := c.Param("id")
	id, err := strconv.Atoi(idStr)
	if err != nil {
		c.JSON(http.StatusBadRequest, models.ApiResponse{
			Success: false,
			Error:   "ID pembayaran tidak valid",
		})
		return
	}

	var req models.UpdatePembayaranRequest
	req.IDPembayaran = id

	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, models.ApiResponse{
			Success: false,
			Error:   err.Error(),
		})
		return
	}

	err = services.UpdatePembayaranStatusService(config.DB, &req)
	if err != nil {
		c.JSON(http.StatusBadRequest, models.ApiResponse{
			Success: false,
			Error:   err.Error(),
		})
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Status pembayaran berhasil diupdate",
	})
}
