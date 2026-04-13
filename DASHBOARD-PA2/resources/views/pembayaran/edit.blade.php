@extends('layouts.app')

@section('title', 'Edit Pembayaran')

@section('content')
<div class="row">
    <div class="col-md-8">
        <h2><i class="bi bi-pencil"></i> Edit Pembayaran</h2>
    </div>
</div>

<div class="card mt-4">
    <div class="card-body">
        <form action="{{ route('pembayaran.update', $pembayaran->id_pembayaran) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="id_tagihan" class="form-label">Tagihan <span class="text-danger">*</span></label>
                <select class="form-control @error('id_tagihan') is-invalid @enderror" id="id_tagihan" name="id_tagihan" required>
                    <option value="">-- Pilih Tagihan --</option>
                    @foreach ($tagihan as $t)
                        <option value="{{ $t->id_tagihan }}" {{ old('id_tagihan', $pembayaran->id_tagihan) == $t->id_tagihan ? 'selected' : '' }}>
                            [#{{ $t->id_tagihan }}] {{ $t->siswa->nama_orgtua }} - Rp {{ number_format($t->jumlah_tagihan, 0, ',', '.') }}
                        </option>
                    @endforeach
                </select>
                @error('id_tagihan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="jumlah_bayar" class="form-label">Jumlah Bayar <span class="text-danger">*</span></label>
                <input type="number" class="form-control @error('jumlah_bayar') is-invalid @enderror" 
                       id="jumlah_bayar" name="jumlah_bayar" value="{{ old('jumlah_bayar', $pembayaran->jumlah_bayar) }}" step="0.01" required>
                @error('jumlah_bayar')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="tgl_pembayaran" class="form-label">Tanggal Pembayaran <span class="text-danger">*</span></label>
                <input type="date" class="form-control @error('tgl_pembayaran') is-invalid @enderror" 
                       id="tgl_pembayaran" name="tgl_pembayaran" value="{{ old('tgl_pembayaran', $pembayaran->tgl_pembayaran->format('Y-m-d')) }}" required>
                @error('tgl_pembayaran')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="status_bayar" class="form-label">Status Pembayaran <span class="text-danger">*</span></label>
                <select class="form-control @error('status_bayar') is-invalid @enderror" id="status_bayar" name="status_bayar" required>
                    <option value="">-- Pilih Status --</option>
                    <option value="menunggu" {{ old('status_bayar', $pembayaran->status_bayar) == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="diterima" {{ old('status_bayar', $pembayaran->status_bayar) == 'diterima' ? 'selected' : '' }}>Diterima</option>
                    <option value="ditolak" {{ old('status_bayar', $pembayaran->status_bayar) == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
                @error('status_bayar')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Perbarui
                </button>
                <a href="{{ route('pembayaran.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
