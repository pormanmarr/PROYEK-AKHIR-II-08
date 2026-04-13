<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TagihanApiController;
use App\Http\Controllers\Api\PembayaranApiController;

Route::middleware('api')->group(function () {
    // Tagihan APIs
    Route::get('/tagihan', [TagihanApiController::class, 'index']);
    Route::get('/tagihan/{id}', [TagihanApiController::class, 'show']);
    
    // Pembayaran APIs
    Route::post('/pembayaran/create', [PembayaranApiController::class, 'create']);
    Route::get('/pembayaran/{transaction_id}/status', [PembayaranApiController::class, 'status']);
    
    // Public webhook endpoint (no auth required)
    Route::post('/webhook/midtrans', [PembayaranApiController::class, 'webhook']);
});
