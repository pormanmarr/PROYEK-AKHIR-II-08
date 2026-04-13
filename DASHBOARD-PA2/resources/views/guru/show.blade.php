@extends('layouts.app')

@section('title', 'Detail Guru')

@section('content')
<div class="row">
    <div class="col-md-8">
        <h2><i class="bi bi-person-circle"></i> Detail Guru</h2>
    </div>
</div>

<div class="card mt-4">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Nama Guru:</strong>
            </div>
            <div class="col-md-9">
                {{ $guru->nama_guru }}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <strong>No. HP:</strong>
            </div>
            <div class="col-md-9">
                {{ $guru->no_hp }}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Email:</strong>
            </div>
            <div class="col-md-9">
                {{ $guru->email }}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Dibuat:</strong>
            </div>
            <div class="col-md-9">
                {{ $guru->created_at->format('d-m-Y H:i') }}
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('guru.edit', $guru->id_guru) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('guru.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>
@endsection
