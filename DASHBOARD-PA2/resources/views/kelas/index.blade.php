@extends('layouts.app')

@section('title', 'Data Kelas')

@section('content')
<div class="row mb-3">
    <div class="col-md-8">
        <h2><i class="bi bi-building"></i> Data Kelas</h2>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('kelas.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Kelas
        </a>
    </div>
</div>

@if ($kelas->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> Tidak ada data kelas
    </div>
@else
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Kelas</th>
                        <th>Guru</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($kelas as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><strong>{{ $item->nama_kelas }}</strong></td>
                        <td>{{ $item->guru->nama_guru ?? '-' }}</td>
                        <td>
                            <a href="{{ route('kelas.show', $item->id_kelas) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i> Lihat
                            </a>
                            <a href="{{ route('kelas.edit', $item->id_kelas) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <form action="{{ route('kelas.destroy', $item->id_kelas) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-danger" data-delete-btn data-item-name="kelas ini">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection
