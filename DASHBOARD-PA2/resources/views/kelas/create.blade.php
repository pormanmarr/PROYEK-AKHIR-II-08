@extends('layouts.app')

@section('title', 'Tambah Kelas')

@section('content')
<div class="row">
    <div class="col-md-8">
        <h2><i class="bi bi-plus-circle"></i> Tambah Kelas Baru</h2>
    </div>
</div>

<div class="card mt-4">
    <div class="card-body">
        <form action="{{ route('kelas.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="id_guru" class="form-label">Guru <span class="text-danger">*</span></label>
                <select class="form-control @error('id_guru') is-invalid @enderror" id="id_guru" name="id_guru" required>
                    <option value="">-- Pilih Guru --</option>
                    @foreach ($guru as $g)
                        <option value="{{ $g->id_guru }}" {{ old('id_guru') == $g->id_guru ? 'selected' : '' }}>
                            {{ $g->nama_guru }}
                        </option>
                    @endforeach
                </select>
                @error('id_guru')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="nama_kelas" class="form-label">Nama Kelas <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('nama_kelas') is-invalid @enderror" 
                       id="nama_kelas" name="nama_kelas" value="{{ old('nama_kelas') }}" required>
                @error('nama_kelas')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Simpan
                </button>
                <a href="{{ route('kelas.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
