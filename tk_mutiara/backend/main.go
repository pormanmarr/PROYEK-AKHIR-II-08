package main

import (
	"log"
	"net/http"

	"github.com/gin-gonic/gin"
	"tk_mutiara_backend/config"
	"tk_mutiara_backend/middleware"
	"tk_mutiara_backend/migrations"
	"tk_mutiara_backend/models"
	"tk_mutiara_backend/repository"
	"tk_mutiara_backend/services"
)

func main() {
	// Load configuration
	if err := config.LoadConfig(); err != nil {
		log.Fatalf("Gagal load config: %v", err)
	}

	// Initialize database
	if err := config.InitDB(config.AppConfig); err != nil {
		log.Fatalf("Gagal init database: %v", err)
	}
	defer config.CloseDB()

	// Initialize schema
	if err := migrations.InitializeSchema(config.DB); err != nil {
		log.Fatalf("Gagal init schema: %v", err)
	}

	// Initialize Gin
	gin.SetMode(config.AppConfig.Environment)
	r := gin.Default()

	// Middleware
	r.Use(middleware.CORSMiddleware())

	// ==============================
	// PUBLIC ROUTES
	// ==============================
	r.GET("/", func(c *gin.Context) {
		c.JSON(http.StatusOK, models.ApiResponse{
			Success: true,
			Message: "Backend TK Mutiara jalan 🚀",
		})
	})

	// Login route
	r.POST("/login", loginHandler)

	// ==============================
	// PROTECTED ROUTES
	// ==============================
	protected := r.Group("/api")
	protected.Use(middleware.AuthMiddleware())
	{
		// Pengumuman routes
		pengumumanRepo := repository.NewPengumumanRepository()
		pengumumanService := services.NewPengumumanService(pengumumanRepo)

		protected.GET("/pengumuman", getPengumuman(pengumumanService))
		protected.GET("/pengumuman/:id", getPengumumanByID(pengumumanService))
		protected.POST("/pengumuman/:id/read", markPengumumanAsRead(pengumumanService))

		// Perkembangan routes
		perkembanganRepo := repository.NewPerkembanganRepository()
		perkembanganService := services.NewPerkembanganService(perkembanganRepo)

		protected.GET("/perkembangan", getPerkembangan(perkembanganService))
		protected.GET("/perkembangan/:id", getPerkembanganByID(perkembanganService))

		// Pembayaran routes
		pembayaranRepo := repository.NewPembayaranRepository()
		pembayaranService := services.NewPembayaranService(pembayaranRepo)

		protected.GET("/pembayaran", getPembayaran(pembayaranService))
		protected.GET("/pembayaran/:id", getPembayaranByID(pembayaranService))
		protected.POST("/pembayaran/bayar", bayarPembayaran(pembayaranService))
	}

	// Start server
	port := ":" + config.AppConfig.ServerPort
	log.Printf("Server berjalan di http://localhost%s\n", port)
	if err := r.Run(port); err != nil {
		log.Fatalf("Gagal menjalankan server: %v", err)
	}
}

// ==============================
// HANDLERS
// ==============================

func loginHandler(c *gin.Context) {
	var req models.LoginRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, models.ApiResponse{
			Success: false,
			Error:   "Format request tidak valid",
		})
		return
	}

	// Dummy user validation (nanti ganti dengan repository)
	if req.Email == "orangtua@tkmutiara.com" && req.Password == "mutiara123" {
		token, err := middleware.GenerateToken(req.Email, "orangtua")
		if err != nil {
			c.JSON(http.StatusInternalServerError, models.ApiResponse{
				Success: false,
				Error:   "Gagal membuat token",
			})
			return
		}

		c.JSON(http.StatusOK, models.ApiResponse{
			Success: true,
			Data: gin.H{
				"token": token,
				"user": gin.H{
					"nama_anak": "Bintang Mutiara",
					"kelas":     "Kelompok A",
					"email":     req.Email,
				},
			},
		})
	} else {
		c.JSON(http.StatusUnauthorized, models.ApiResponse{
			Success: false,
			Error:   "Email atau password salah",
		})
	}
}

func getPengumuman(svc *services.PengumumanService) gin.HandlerFunc {
	return func(c *gin.Context) {
		data, err := svc.GetAll()
		if err != nil {
			c.JSON(http.StatusInternalServerError, models.ApiResponse{
				Success: false,
				Error:   "Gagal mengambil data pengumuman",
			})
			return
		}
		c.JSON(http.StatusOK, models.ApiResponse{
			Success: true,
			Data:    data,
		})
	}
}

func getPengumumanByID(svc *services.PengumumanService) gin.HandlerFunc {
	return func(c *gin.Context) {
		id := c.Param("id")
		data, err := svc.GetByID(id)
		if err != nil {
			c.JSON(http.StatusNotFound, models.ApiResponse{
				Success: false,
				Error:   "Pengumuman tidak ditemukan",
			})
			return
		}
		c.JSON(http.StatusOK, models.ApiResponse{
			Success: true,
			Data:    data,
		})
	}
}

func markPengumumanAsRead(svc *services.PengumumanService) gin.HandlerFunc {
	return func(c *gin.Context) {
		id := c.Param("id")
		if err := svc.MarkAsRead(id); err != nil {
			c.JSON(http.StatusInternalServerError, models.ApiResponse{
				Success: false,
				Error:   "Gagal menandai pengumuman",
			})
			return
		}
		c.JSON(http.StatusOK, models.ApiResponse{
			Success: true,
			Message: "Pengumuman berhasil ditandai",
		})
	}
}

func getPerkembangan(svc *services.PerkembanganService) gin.HandlerFunc {
	return func(c *gin.Context) {
		data, err := svc.GetAll()
		if err != nil {
			c.JSON(http.StatusInternalServerError, models.ApiResponse{
				Success: false,
				Error:   "Gagal mengambil data perkembangan",
			})
			return
		}
		c.JSON(http.StatusOK, models.ApiResponse{
			Success: true,
			Data:    data,
		})
	}
}

func getPerkembanganByID(svc *services.PerkembanganService) gin.HandlerFunc {
	return func(c *gin.Context) {
		id := c.Param("id")
		data, err := svc.GetByID(id)
		if err != nil {
			c.JSON(http.StatusNotFound, models.ApiResponse{
				Success: false,
				Error:   "Perkembangan tidak ditemukan",
			})
			return
		}
		c.JSON(http.StatusOK, models.ApiResponse{
			Success: true,
			Data:    data,
		})
	}
}

func getPembayaran(svc *services.PembayaranService) gin.HandlerFunc {
	return func(c *gin.Context) {
		data, err := svc.GetAll()
		if err != nil {
			c.JSON(http.StatusInternalServerError, models.ApiResponse{
				Success: false,
				Error:   "Gagal mengambil data pembayaran",
			})
			return
		}
		c.JSON(http.StatusOK, models.ApiResponse{
			Success: true,
			Data:    data,
		})
	}
}

func getPembayaranByID(svc *services.PembayaranService) gin.HandlerFunc {
	return func(c *gin.Context) {
		id := c.Param("id")
		data, err := svc.GetByID(id)
		if err != nil {
			c.JSON(http.StatusNotFound, models.ApiResponse{
				Success: false,
				Error:   "Pembayaran tidak ditemukan",
			})
			return
		}
		c.JSON(http.StatusOK, models.ApiResponse{
			Success: true,
			Data:    data,
		})
	}
}

func bayarPembayaran(svc *services.PembayaranService) gin.HandlerFunc {
	return func(c *gin.Context) {
		var req models.BayarRequest
		if err := c.ShouldBindJSON(&req); err != nil {
			c.JSON(http.StatusBadRequest, models.ApiResponse{
				Success: false,
				Error:   "Format request tidak valid",
			})
			return
		}

		result, err := svc.ProcessPayment(req.ID, req.Metode)
		if err != nil {
			c.JSON(http.StatusInternalServerError, models.ApiResponse{
				Success: false,
				Error:   "Gagal memproses pembayaran",
			})
			return
		}

		c.JSON(http.StatusOK, models.ApiResponse{
			Success: true,
			Message: "Pembayaran berhasil diproses",
			Data:    result,
		})
	}
}