@extends('layouts.app')

@section('title', 'Data Pengumuman')

@section('content')
<div class="row mb-3">
    <div class="col-md-8">
        <h2><i class="bi bi-megaphone"></i> Data Pengumuman</h2>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('pengumuman.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Buat Pengumuman
        </a>
    </div>
</div>

@if ($pengumuman->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> Tidak ada pengumuman
    </div>
@else
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Foto</th>
                        <th>Judul</th>
                        <th>Guru</th>
                        <th>Waktu Unggah</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pengumuman as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            @if($item->media)
                                <img src="{{ asset('storage/' . $item->media) }}" alt="Thumbnail" 
                                     class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                            @else
                                <span class="badge bg-secondary">No Media</span>
                            @endif
                        </td>
                        <td><strong>{{ $item->judul }}</strong></td>
                        <td>{{ $item->guru->nama_guru ?? '-' }}</td>
                        <td><small>{{ $item->waktu_unggah->format('d-m-Y H:i') }}</small></td>
                        <td>
                            <a href="{{ route('pengumuman.show', $item->id_pengumuman) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i> Lihat
                            </a>
                            <a href="{{ route('pengumuman.edit', $item->id_pengumuman) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <form action="{{ route('pengumuman.destroy', $item->id_pengumuman) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-danger" data-delete-btn data-item-name="pengumuman ini beserta foto">
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
