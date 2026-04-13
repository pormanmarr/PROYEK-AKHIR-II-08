@extends('layouts.app')

@section('title', 'Detail Akun')

@section('content')
<div class="row">
    <div class="col-md-8">
        <h2><i class="bi bi-key"></i> Detail Akun</h2>
    </div>
</div>

<div class="card mt-4">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Username:</strong>
            </div>
            <div class="col-md-9">
                {{ $akun->username }}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Role:</strong>
            </div>
            <div class="col-md-9">
                <span class="badge {{ $akun->role == 'guru' ? 'bg-primary' : 'bg-success' }}">
                    {{ ucfirst($akun->role) }}
                </span>
            </div>
        </div>

        @if ($akun->guru)
        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Guru:</strong>
            </div>
            <div class="col-md-9">
                {{ $akun->guru->nama_guru }}
            </div>
        </div>
        @endif

        @if ($akun->siswa)
        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Siswa:</strong>
            </div>
            <div class="col-md-9">
                {{ $akun->siswa->nama_siswa }} ({{ $akun->siswa->nama_orgtua }})
            </div>
        </div>
        @endif

        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Dibuat:</strong>
            </div>
            <div class="col-md-9">
                {{ $akun->created_at->format('d-m-Y H:i') }}
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('akun.edit', $akun->id_akun) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('akun.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>
@endsection
