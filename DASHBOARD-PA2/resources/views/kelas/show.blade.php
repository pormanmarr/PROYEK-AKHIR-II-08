@extends('layouts.app')

@section('title', 'Detail Kelas')

@section('content')
<div class="row">
    <div class="col-md-8">
        <h2><i class="bi bi-building"></i> Detail Kelas</h2>
    </div>
</div>

<div class="card mt-4">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Nama Kelas:</strong>
            </div>
            <div class="col-md-9">
                {{ $kelas->nama_kelas }}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Guru:</strong>
            </div>
            <div class="col-md-9">
                {{ $kelas->guru->nama_guru ?? '-' }}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Dibuat:</strong>
            </div>
            <div class="col-md-9">
                {{ $kelas->created_at->format('d-m-Y H:i') }}
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('kelas.edit', $kelas->id_kelas) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('kelas.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>
@endsection
