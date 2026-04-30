package services

import (
	"bytes"
	"crypto/sha512"
	"database/sql"
	"encoding/base64"
	"encoding/hex"
	"encoding/json"
	"fmt"
	"io"
	"net/http"
	"strings"
	"time"

	"tk_mutiara_backend/config"
	"tk_mutiara_backend/models"
	"tk_mutiara_backend/repository"
)

type midtransSnapRequest struct {
	TransactionDetails midtransTransactionDetails `json:"transaction_details"`
	CustomerDetails    midtransCustomerDetails    `json:"customer_details"`
	ItemDetails        []midtransItemDetail       `json:"item_details"`
}

type midtransTransactionDetails struct {
	OrderID     string  `json:"order_id"`
	GrossAmount float64 `json:"gross_amount"`
}

type midtransCustomerDetails struct {
	FirstName string `json:"first_name"`
}

type midtransItemDetail struct {
	ID       string  `json:"id"`
	Price    float64 `json:"price"`
	Quantity int     `json:"quantity"`
	Name     string  `json:"name"`
}

type midtransSnapResponse struct {
	Token       string `json:"token"`
	RedirectURL string `json:"redirect_url"`
}

// CreateMidtransTransaction membuat transaksi baru ke Midtrans Snap dan menyimpan draft pembayaran.
func CreateMidtransTransaction(db *sql.DB, nomorIndukSiswa string, idTagihan int) (*models.CreateMidtransTransactionResponse, error) {
	if strings.TrimSpace(config.AppConfig.MidtransServerKey) == "" {
		return nil, fmt.Errorf("MIDTRANS_SERVER_KEY belum diatur")
	}

	tagihan, err := repository.GetTagihanForPaymentByIDAndSiswa(db, idTagihan, nomorIndukSiswa)
	if err != nil {
		return nil, err
	}

	if tagihan.StatusTagihan == "lunas" {
		return nil, fmt.Errorf("tagihan sudah lunas")
	}

	orderID := fmt.Sprintf("TAGIHAN-%d-%d", tagihan.IDTagihan, time.Now().Unix())

	payload := midtransSnapRequest{
		TransactionDetails: midtransTransactionDetails{
			OrderID:     orderID,
			GrossAmount: tagihan.JumlahTagihan,
		},
		CustomerDetails: midtransCustomerDetails{
			FirstName: strings.TrimSpace(tagihan.NamaOrangtua),
		},
		ItemDetails: []midtransItemDetail{
			{
				ID:       fmt.Sprintf("tagihan-%d", tagihan.IDTagihan),
				Price:    tagihan.JumlahTagihan,
				Quantity: 1,
				Name:     fmt.Sprintf("SPP %s - %s", tagihan.Periode, tagihan.NamaSiswa),
			},
		},
	}

	if payload.CustomerDetails.FirstName == "" {
		payload.CustomerDetails.FirstName = "Orangtua"
	}

	snapResp, err := requestMidtransSnap(payload)
	if err != nil {
		return nil, err
	}

	idPembayaran, err := repository.CreatePembayaranPending(db, tagihan.IDTagihan, tagihan.JumlahTagihan, orderID)
	if err != nil {
		return nil, err
	}

	if err := repository.UpdatePembayaranSnapResponse(db, idPembayaran, snapResp.Token, snapResp.RedirectURL); err != nil {
		return nil, err
	}

	return &models.CreateMidtransTransactionResponse{
		IDTagihan:     tagihan.IDTagihan,
		IDPembayaran:  idPembayaran,
		OrderID:       orderID,
		SnapToken:     snapResp.Token,
		RedirectURL:   snapResp.RedirectURL,
		Amount:        tagihan.JumlahTagihan,
		StatusTagihan: tagihan.StatusTagihan,
		StatusBayar:   "menunggu",
		ClientKey:     config.AppConfig.MidtransClientKey,
	}, nil
}

func requestMidtransSnap(payload midtransSnapRequest) (*midtransSnapResponse, error) {
	baseURL := "https://app.sandbox.midtrans.com"
	if strings.EqualFold(config.AppConfig.MidtransEnvironment, "production") {
		baseURL = "https://app.midtrans.com"
	}

	body, err := json.Marshal(payload)
	if err != nil {
		return nil, fmt.Errorf("gagal encode payload midtrans: %w", err)
	}

	req, err := http.NewRequest(http.MethodPost, baseURL+"/snap/v1/transactions", bytes.NewBuffer(body))
	if err != nil {
		return nil, fmt.Errorf("gagal membuat request midtrans: %w", err)
	}

	auth := base64.StdEncoding.EncodeToString([]byte(config.AppConfig.MidtransServerKey + ":"))
	req.Header.Set("Authorization", "Basic "+auth)
	req.Header.Set("Content-Type", "application/json")
	req.Header.Set("Accept", "application/json")

	client := &http.Client{Timeout: 20 * time.Second}
	resp, err := client.Do(req)
	if err != nil {
		return nil, fmt.Errorf("gagal menghubungi midtrans: %w", err)
	}
	defer resp.Body.Close()

	respBytes, _ := io.ReadAll(resp.Body)
	if resp.StatusCode < 200 || resp.StatusCode >= 300 {
		return nil, fmt.Errorf("midtrans response %d: %s", resp.StatusCode, string(respBytes))
	}

	var parsed midtransSnapResponse
	if err := json.Unmarshal(respBytes, &parsed); err != nil {
		return nil, fmt.Errorf("gagal parsing response midtrans: %w", err)
	}
	if strings.TrimSpace(parsed.Token) == "" {
		return nil, fmt.Errorf("snap token tidak diterima dari midtrans")
	}

	return &parsed, nil
}

// fetchMidtransStatusAPI mengambil status pembayaran dari API Midtrans langsung
func fetchMidtransStatusAPI(orderID string) (*models.MidtransNotification, error) {
	baseURL := "https://api.sandbox.midtrans.com"
	if strings.EqualFold(config.AppConfig.MidtransEnvironment, "production") {
		baseURL = "https://api.midtrans.com"
	}

	req, err := http.NewRequest(http.MethodGet, baseURL+"/v2/"+orderID+"/status", nil)
	if err != nil {
		return nil, fmt.Errorf("gagal membuat request midtrans status: %w", err)
	}

	auth := base64.StdEncoding.EncodeToString([]byte(config.AppConfig.MidtransServerKey + ":"))
	req.Header.Set("Authorization", "Basic "+auth)
	req.Header.Set("Accept", "application/json")

	client := &http.Client{Timeout: 15 * time.Second}
	resp, err := client.Do(req)
	if err != nil {
		return nil, fmt.Errorf("gagal menghubungi midtrans status api: %w", err)
	}
	defer resp.Body.Close()

	if resp.StatusCode < 200 || resp.StatusCode >= 300 {
		respBytes, _ := io.ReadAll(resp.Body)
		return nil, fmt.Errorf("midtrans API response %d: %s", resp.StatusCode, string(respBytes))
	}

	var parsed models.MidtransNotification
	if err := json.NewDecoder(resp.Body).Decode(&parsed); err != nil {
		return nil, fmt.Errorf("gagal parsing response midtrans status: %w", err)
	}

	return &parsed, nil
}

// processMidtransStatus berisi logika internal update database saat status Midtrans diterima
func processMidtransStatus(db *sql.DB, payload *models.MidtransNotification) error {
	rawPayload, _ := json.Marshal(payload)
	if err := repository.UpdatePembayaranMidtransStatus(
		db,
		payload.OrderID,
		payload.TransactionID,
		payload.TransactionStatus,
		payload.PaymentType,
		payload.FraudStatus,
		string(rawPayload),
	); err != nil {
		return err
	}

	if isMidtransSuccess(payload.TransactionStatus, payload.FraudStatus) {
		if err := repository.MarkPembayaranLunasByOrderID(db, payload.OrderID); err != nil {
			return err
		}
	}

	if err := repository.SyncTagihanStatusByOrderID(db, payload.OrderID); err != nil {
		return err
	}

	return nil
}

// HandleMidtransWebhook memproses notifikasi Midtrans dan update status pembayaran/tagihan.
func HandleMidtransWebhook(db *sql.DB, payload *models.MidtransNotification) error {
	if !isValidMidtransSignature(payload) {
		return fmt.Errorf("signature key midtrans tidak valid")
	}

	return processMidtransStatus(db, payload)
}

func isValidMidtransSignature(payload *models.MidtransNotification) bool {
	raw := payload.OrderID + payload.StatusCode + payload.GrossAmount + config.AppConfig.MidtransServerKey
	hash := sha512.Sum512([]byte(raw))
	expected := hex.EncodeToString(hash[:])
	return strings.EqualFold(expected, payload.SignatureKey)
}

func isMidtransSuccess(status, fraudStatus string) bool {
	s := strings.ToLower(strings.TrimSpace(status))
	f := strings.ToLower(strings.TrimSpace(fraudStatus))

	if s == "settlement" {
		return true
	}

	if s == "capture" {
		if f == "" || f == "accept" {
			return true
		}
	}

	return false
}

// GetPaymentStatusByTagihan untuk polling status di app parent.
func GetPaymentStatusByTagihan(db *sql.DB, nomorIndukSiswa string, idTagihan int) (*models.PaymentStatusResponse, error) {
	if idTagihan <= 0 {
		return nil, fmt.Errorf("id tagihan tidak valid")
	}
	if strings.TrimSpace(nomorIndukSiswa) == "" {
		return nil, fmt.Errorf("nomor induk siswa tidak valid")
	}

	res, err := repository.GetPaymentStatusByTagihanAndSiswa(db, idTagihan, nomorIndukSiswa)
	if err != nil {
		return nil, err
	}

	// Cek status ke midtrans jika belum lunas dan punya order id
	if res.StatusTagihan != "lunas" && res.OrderID != "" {
		midtransStatus, errAPI := fetchMidtransStatusAPI(res.OrderID)
		if errAPI == nil && midtransStatus != nil && midtransStatus.TransactionStatus != "" {
			_ = processMidtransStatus(db, midtransStatus)
			
			// Ambil ulang status terbaru dari DB setelah di update
			newRes, errDB := repository.GetPaymentStatusByTagihanAndSiswa(db, idTagihan, nomorIndukSiswa)
			if errDB == nil {
				return newRes, nil
			}
		}
	}

	return res, nil
}
