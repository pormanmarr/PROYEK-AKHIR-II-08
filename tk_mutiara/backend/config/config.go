package config

import (
	"fmt"
	"os"
	"strconv"

	"github.com/joho/godotenv"
)

type Config struct {
	// Database
	DBHost     string
	DBPort     string
	DBUser     string
	DBPassword string
	DBName     string
	DBSSLMode  string

	// Server
	ServerPort string

	// JWT
	JWTSecret      string
	JWTExpiration  int
	Environment    string
}

var AppConfig *Config

// LoadConfig memuat konfigurasi dari file .env
func LoadConfig() error {
	// Load .env file
	_ = godotenv.Load()

	jwtExp := os.Getenv("JWT_EXPIRATION")
	if jwtExp == "" {
		jwtExp = "24"
	}
	exp, _ := strconv.Atoi(jwtExp)

	AppConfig = &Config{
		// Database
		DBHost:     getEnv("DB_HOST", "localhost"),
		DBPort:     getEnv("DB_PORT", "5432"),
		DBUser:     getEnv("DB_USER", "postgres"),
		DBPassword: getEnv("DB_PASSWORD", ""),
		DBName:     getEnv("DB_NAME", "tk_mutiara"),
		DBSSLMode:  getEnv("DB_SSL_MODE", "disable"),

		// Server
		ServerPort: getEnv("SERVER_PORT", "8081"),

		// JWT
		JWTSecret:     getEnv("JWT_SECRET", "tk_mutiara_secret_key"),
		JWTExpiration: exp,
		Environment:   getEnv("ENVIRONMENT", "development"),
	}

	return nil
}

// GetDSN mengembalikan string koneksi database
func (c *Config) GetDSN() string {
	return fmt.Sprintf(
		"host=%s port=%s user=%s password=%s dbname=%s sslmode=%s",
		c.DBHost, c.DBPort, c.DBUser, c.DBPassword, c.DBName, c.DBSSLMode,
	)
}

func getEnv(key, defaultValue string) string {
	if value, exists := os.LookupEnv(key); exists {
		return value
	}
	return defaultValue
}
