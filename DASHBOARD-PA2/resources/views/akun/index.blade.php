@extends('layouts.app')

@section('title', 'Kelola Akun')

@section('content')
<div class="row mb-3">
    <div class="col-md-8">
        <h2><i class="bi bi-key"></i> Kelola Akun</h2>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('akun.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Generate Akun
        </a>
    </div>
</div>

@if ($akun->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> Tidak ada akun
    </div>
@else
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Guru</th>
                        <th>Siswa</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($akun as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><strong>{{ $item->username }}</strong></td>
                        <td>
                            <span class="badge {{ $item->role == 'guru' ? 'bg-primary' : 'bg-success' }}">
                                {{ ucfirst($item->role) }}
                            </span>
                        </td>
                        <td>
                            @if ($item->role === 'guru')
                                @if ($item->is_super_admin)
                                    <span class="badge bg-danger"><i class="bi bi-shield-fill"></i> Super Admin</span>
                                @else
                                    <span class="badge bg-secondary">Regular Guru</span>
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $item->guru->nama_guru ?? '-' }}</td>
                        <td>{{ $item->siswa->nama_siswa ?? '-' }}</td>
                        <td>
                            <a href="{{ route('akun.show', $item->id_akun) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i> Lihat
                            </a>
                            <a href="{{ route('akun.edit', $item->id_akun) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <form action="{{ route('akun.destroy', $item->id_akun) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-danger" data-delete-btn data-item-name="akun ini">
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
