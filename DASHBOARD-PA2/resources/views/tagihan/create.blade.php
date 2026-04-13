@extends('layouts.app')

@section('title', 'Buat Tagihan')

@section('content')
<div class="row">
    <div class="col-md-8">
        <h2><i class="bi bi-plus-circle"></i> Buat Tagihan Baru</h2>
    </div>
</div>

<div class="card mt-4">
    <div class="card-body">
        <form action="{{ route('tagihan.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="nomor_induk_siswa" class="form-label">Siswa <span class="text-danger">*</span></label>
                <select class="form-control @error('nomor_induk_siswa') is-invalid @enderror" id="nomor_induk_siswa" name="nomor_induk_siswa" required>
                    <option value="">-- Pilih Siswa --</option>
                    @foreach ($siswa as $s)
                        <option value="{{ $s->nomor_induk_siswa }}" {{ old('nomor_induk_siswa') == $s->nomor_induk_siswa ? 'selected' : '' }}>
                            {{ $s->nama_siswa }}
                        </option>
                    @endforeach
                </select>
                @error('nomor_induk_siswa')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="jumlah_tagihan" class="form-label">Jumlah Tagihan <span class="text-danger">*</span></label>
                <input type="number" class="form-control @error('jumlah_tagihan') is-invalid @enderror" 
                       id="jumlah_tagihan" name="jumlah_tagihan" value="{{ old('jumlah_tagihan') }}" step="0.01" required>
                @error('jumlah_tagihan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="periode" class="form-label">Periode <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('periode') is-invalid @enderror" 
                       id="periode" name="periode" value="{{ old('periode') }}" placeholder="Misal: Januari 2026" required>
                @error('periode')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="alert alert-info" role="alert">
                <i class="bi bi-info-circle"></i> <strong>Info:</strong> Status pembayaran akan berubah otomatis menjadi "Lunas" ketika orangtua melakukan pembayaran melalui aplikasi mobile. Anda tidak dapat mengubah status secara manual.
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Simpan
                </button>
                <a href="{{ route('tagihan.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
