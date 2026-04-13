@extends('layouts.app')

@section('title', 'Tambah Guru')

@section('content')
<div class="row">
    <div class="col-md-8">
        <h2><i class="bi bi-plus-circle"></i> Tambah Guru Baru</h2>
    </div>
</div>

<div class="card mt-4">
    <div class="card-body">
        <form action="{{ route('guru.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="nama_guru" class="form-label">Nama Guru <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('nama_guru') is-invalid @enderror" 
                       id="nama_guru" name="nama_guru" value="{{ old('nama_guru') }}" required>
                @error('nama_guru')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="no_hp" class="form-label">No. HP <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('no_hp') is-invalid @enderror" 
                       id="no_hp" name="no_hp" value="{{ old('no_hp') }}" required>
                @error('no_hp')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                       id="email" name="email" value="{{ old('email') }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Simpan
                </button>
                <a href="{{ route('guru.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
