<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class PembayaranApiController extends Controller
{
    public function __construct()
    {
        // Set Midtrans Config
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$clientKey = config('services.midtrans.client_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Create payment transaction & get Snap token
     * POST /api/pembayaran/create
     */
    public function create(Request $request)
    {
        try {
            $request->validate([
                'id_tagihan' => 'required|exists:tagihan,id_tagihan',
            ]);

            $tagihan = Tagihan::with('siswa')->findOrFail($request->id_tagihan);

            // Check if already paid
            if ($tagihan->payment_status === 'lunas') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tagihan sudah lunas',
                ], 400);
            }

            // Generate transaction ID (unique)
            $transactionId = 'TAG-' . $tagihan->id_tagihan . '-' . time();

            // Prepare payment details
            $transaction_details = array(
                'order_id' => $transactionId,
                'gross_amount' => (int)$tagihan->jumlah_tagihan,
            );

            $customer_details = array(
                'first_name' => $tagihan->siswa->nama_siswa,
                'email' => 'orangtua@school.com',
                'phone' => '6200000000000',
            );

            $payload = array(
                'transaction_details' => $transaction_details,
                'customer_details' => $customer_details,
                'item_details' => [
                    [
                        'id' => 'tagihan-' . $tagihan->id_tagihan,
                        'price' => (int)$tagihan->jumlah_tagihan,
                        'quantity' => 1,
                        'name' => 'SPP ' . $tagihan->periode . ' - ' . $tagihan->siswa->nama_siswa,
                    ]
                ],
            );

            // Get Snap Token
            $snapToken = Snap::getSnapToken($payload);

            // Store transaction_id temporarily in tagihan
            $tagihan->update([
                'transaction_id' => $transactionId,
                'payment_method' => $request->input('payment_method', 'unknown'),
            ]);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'snap_token' => $snapToken,
                    'transaction_id' => $transactionId,
                    'order_id' => $transactionId,
                    'gross_amount' => $tagihan->jumlah_tagihan,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Check payment status
     * GET /api/pembayaran/{transaction_id}/status
     */
    public function status($transaction_id)
    {
        try {
            // Find tagihan by transaction_id dulu
            $tagihan = Tagihan::where('transaction_id', $transaction_id)->firstOrFail();

            // Jika payment_status sudah "lunas" di database, langsung return tanpa query Midtrans
            if ($tagihan->payment_status === 'lunas') {
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'transaction_id' => $transaction_id,
                        'payment_status' => 'lunas',
                        'payment_method' => $tagihan->payment_method ?? 'unknown',
                        'transaction_status' => 'settlement',
                        'gross_amount' => $tagihan->jumlah_tagihan,
                        'currency' => 'IDR',
                    ],
                ], 200);
            }

            // Get status from Midtrans untuk status pending
            try {
                $status = Transaction::status($transaction_id);
                $paymentStatus = $this->mapMidtransStatus($status->transaction_status);

                // Update tagihan status jika berubah
                if ($tagihan->payment_status !== $paymentStatus) {
                    $tagihan->update([
                        'payment_status' => $paymentStatus,
                        'payment_method' => $status->payment_type ?? null,
                    ]);

                    // If payment success, update payment_date
                    if ($paymentStatus === 'lunas') {
                        $tagihan->update([
                            'payment_date' => now(),
                        ]);
                    }
                }

                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'transaction_id' => $transaction_id,
                        'payment_status' => $paymentStatus,
                        'payment_method' => $status->payment_type ?? 'unknown',
                        'transaction_status' => $status->transaction_status,
                        'gross_amount' => $status->gross_amount,
                        'currency' => $status->currency ?? 'IDR',
                    ],
                ], 200);
            } catch (\Exception $midtransError) {
                // Jika Midtrans error (transaction belum ada di Midtrans), kembalikan status dari database
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'transaction_id' => $transaction_id,
                        'payment_status' => $tagihan->payment_status ?? 'belum_bayar',
                        'payment_method' => $tagihan->payment_method ?? 'unknown',
                        'transaction_status' => $tagihan->payment_status ?? 'belum_bayar',
                        'gross_amount' => $tagihan->jumlah_tagihan,
                        'currency' => 'IDR',
                    ],
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Status tidak ditemukan: ' . $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Webhook handler from Midtrans
     * POST /webhook/midtrans
     * No authentication required - Midtrans verifies signature
     */
    public function webhook(Request $request)
    {
        try {
            $notif = json_decode($request->getContent());
            $transactionId = $notif->order_id ?? null;

            if (!$transactionId) {
                return response()->json(['status' => 'error'], 400);
            }

            // Get status from Midtrans
            $status = Transaction::status($transactionId);
            $paymentStatus = $this->mapMidtransStatus($status->transaction_status);

            // Find tagihan
            $tagihan = Tagihan::where('transaction_id', $transactionId)->first();

            if (!$tagihan) {
                \Log::warning("Webhook received for unknown transaction: $transactionId");
                return response()->json(['status' => 'warning'], 200);
            }

            // Update tagihan dengan status terbaru
            $updateData = [
                'payment_status' => $paymentStatus,
                'payment_method' => $status->payment_type ?? null,
            ];

            // Set payment_date jika lunas
            if ($paymentStatus === 'lunas') {
                $updateData['payment_date'] = now();
            }

            $tagihan->update($updateData);

            // Log webhook
            \Log::info("Webhook processed - Transaction: $transactionId, Status: $paymentStatus");

            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            \Log::error("Webhook error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Map Midtrans transaction status to application payment_status
     * Status values: belum_bayar, lunas only (no pending/gagal for user display)
     */
    private function mapMidtransStatus($transactionStatus)
    {
        switch ($transactionStatus) {
            case 'capture':
            case 'settlement':
                return 'lunas';
            case 'pending':
            case 'expire':
            case 'cancel':
            case 'deny':
                return 'belum_bayar'; // Failed or expired → back to unpaid
            default:
                return 'belum_bayar';
        }
    }
}
