@extends('layouts.app')

@section('title', 'Tambah Siswa')

@section('content')
<div class="row">
    <div class="col-md-8">
        <h2><i class="bi bi-plus-circle"></i> Tambah Siswa Baru</h2>
    </div>
</div>

<div class="card mt-4">
    <div class="card-body">
        <form action="{{ route('siswa.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="id_kelas" class="form-label">Kelas <span class="text-danger">*</span></label>
                <select class="form-control @error('id_kelas') is-invalid @enderror" id="id_kelas" name="id_kelas" required>
                    <option value="">-- Pilih Kelas --</option>
                    @foreach ($kelas as $k)
                        <option value="{{ $k->id_kelas }}" {{ old('id_kelas') == $k->id_kelas ? 'selected' : '' }}>
                            {{ $k->nama_kelas }}
                        </option>
                    @endforeach
                </select>
                @error('id_kelas')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="nomor_induk_siswa" class="form-label">Nomor Induk Siswa (NIS) <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('nomor_induk_siswa') is-invalid @enderror" 
                       id="nomor_induk_siswa" name="nomor_induk_siswa" value="{{ old('nomor_induk_siswa') }}" 
                       pattern="[0-9]*" maxlength="20" required style="font-size: 1.1em; letter-spacing: 0.05em;">
                @error('nomor_induk_siswa')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Masukkan nomor induk siswa yang unik (tidak boleh sama dengan siswa lain). Contoh: 0064424163</small>
            </div>

            <div class="mb-3">
                <label for="nama_siswa" class="form-label">Nama Siswa <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('nama_siswa') is-invalid @enderror" 
                       id="nama_siswa" name="nama_siswa" value="{{ old('nama_siswa') }}" required>
                @error('nama_siswa')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="nama_orgtua" class="form-label">Nama Orangtua <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('nama_orgtua') is-invalid @enderror" 
                       id="nama_orgtua" name="nama_orgtua" value="{{ old('nama_orgtua') }}" required>
                @error('nama_orgtua')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="tgl_lahir" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                <input type="date" class="form-control @error('tgl_lahir') is-invalid @enderror" 
                       id="tgl_lahir" name="tgl_lahir" value="{{ old('tgl_lahir') }}" required>
                @error('tgl_lahir')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                <select class="form-control @error('jenis_kelamin') is-invalid @enderror" id="jenis_kelamin" name="jenis_kelamin" required>
                    <option value="">-- Pilih --</option>
                    <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
                @error('jenis_kelamin')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                <textarea class="form-control @error('alamat') is-invalid @enderror" 
                          id="alamat" name="alamat" rows="4" required>{{ old('alamat') }}</textarea>
                @error('alamat')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Simpan
                </button>
                <a href="{{ route('siswa.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
