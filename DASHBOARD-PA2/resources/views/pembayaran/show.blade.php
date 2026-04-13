@extends('layouts.app')

@section('title', 'Detail Pembayaran')

@section('content')
<div class="row">
    <div class="col-md-8">
        <h2><i class="bi bi-credit-card"></i> Detail Pembayaran</h2>
    </div>
</div>

<div class="card mt-4">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-3">
                <strong>ID Pembayaran:</strong>
            </div>
            <div class="col-md-9">
                {{ $pembayaran->id_pembayaran }}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Tagihan:</strong>
            </div>
            <div class="col-md-9">
                #{{ $pembayaran->tagihan->id_tagihan }}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Jumlah Bayar:</strong>
            </div>
            <div class="col-md-9">
                Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Tanggal Pembayaran:</strong>
            </div>
            <div class="col-md-9">
                {{ $pembayaran->tgl_pembayaran->format('d-m-Y') }}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Status Pembayaran:</strong>
            </div>
            <div class="col-md-9">
                <span class="badge 
                    @if($pembayaran->status_bayar == 'diterima') bg-success
                    @elseif($pembayaran->status_bayar == 'menunggu') bg-warning
                    @else bg-danger
                    @endif">
                    @php
                        $statusLabels = [
                            'diterima' => 'Diterima',
                            'menunggu' => 'Menunggu',
                            'ditolak' => 'Ditolak'
                        ];
                        $badgeColor = $pembayaran->status_bayar == 'diterima' ? 'bg-success' : ($pembayaran->status_bayar == 'menunggu' ? 'bg-warning' : 'bg-danger');
                    @endphp
                    <span class="badge {{ $badgeColor }}">
                        {{ $statusLabels[$pembayaran->status_bayar] ?? ucfirst($pembayaran->status_bayar) }}
                    </span>
                </span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Dibuat:</strong>
            </div>
            <div class="col-md-9">
                {{ $pembayaran->created_at->format('d-m-Y H:i') }}
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('pembayaran.edit', $pembayaran->id_pembayaran) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('pembayaran.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>
@endsection
