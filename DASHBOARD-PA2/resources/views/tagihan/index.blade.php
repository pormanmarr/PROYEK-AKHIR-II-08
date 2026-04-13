@extends('layouts.app')

@section('title', 'Data Tagihan')

@section('content')
<div class="row mb-3">
    <div class="col-md-6">
        <h2><i class="bi bi-receipt"></i> Data Tagihan</h2>
    </div>
    <div class="col-md-6 text-end d-flex gap-2 justify-content-end flex-wrap">
        <a href="{{ route('tagihan.bulkCreate') }}" class="btn btn-info">
            <i class="bi bi-files"></i> Buat Massal
        </a>
        <a href="{{ route('tagihan.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Buat Tagihan
        </a>
    </div>
</div>

<!-- Info Box -->
<div class="alert alert-info alert-dismissible fade show" role="alert">
    <i class="bi bi-info-circle"></i> <strong>Perhatian:</strong> Status pembayaran berubah otomatis menjadi "Lunas" ketika orangtua melakukan pembayaran melalui aplikasi mobile. Anda tidak dapat mengubah status secara manual.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<!-- Filter Section -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h6 class="mb-0"><i class="bi bi-funnel"></i> Filter Data Tagihan</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('tagihan.index') }}" method="GET" class="row g-3">
            <!-- Filter NIS -->
            <div class="col-md-3">
                <label for="nis" class="form-label">NIS</label>
                <input type="text" class="form-control" id="nis" name="nis" 
                       value="{{ request('nis') }}" placeholder="Cari NIS...">
            </div>

            <!-- Filter Nama Siswa -->
            <div class="col-md-3">
                <label for="nama" class="form-label">Nama Siswa</label>
                <input type="text" class="form-control" id="nama" name="nama" 
                       value="{{ request('nama') }}" placeholder="Cari nama siswa...">
            </div>

            <!-- Filter Kelas -->
            <div class="col-md-3">
                <label for="kelas" class="form-label">Kelas</label>
                <select class="form-select" id="kelas" name="kelas">
                    <option value="">-- Semua Kelas --</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id_kelas }}" {{ request('kelas') == $k->id_kelas ? 'selected' : '' }}>
                            {{ $k->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Periode -->
            <div class="col-md-3">
                <label for="periode" class="form-label">Periode</label>
                <select class="form-select" id="periode" name="periode">
                    <option value="">-- Semua Periode --</option>
                    @foreach($periode as $p)
                        <option value="{{ $p }}" {{ request('periode') == $p ? 'selected' : '' }}>
                            {{ $p }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status -->
            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">-- Semua --</option>
                    @foreach($statuses as $key => $label)
                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="col-12">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Cari
                </button>
                <a href="{{ route('tagihan.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-clockwise"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

@if ($tagihan->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> 
        @if(request()->hasAny(['nis', 'nama', 'kelas', 'periode', 'status']))
            Tidak ada data tagihan yang sesuai dengan filter Anda
        @else
            Tidak ada data tagihan
        @endif
    </div>
@else
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>NIS</th>
                        <th>Siswa</th>
                        <th>Kelas</th>
                        <th>Periode</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tagihan as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            @if($item->siswa)
                                {{ $item->siswa->nomor_induk_siswa }}
                            @else
                                <span style="color: red;">NULL SISWA (NIS: {{ $item->nomor_induk_siswa }})</span>
                            @endif
                        </td>
                        <td>
                            @if($item->siswa)
                                {{ $item->siswa->nama_siswa }}
                            @else
                                <span style="color: red;">-</span>
                            @endif
                        </td>
                        <td>
                            @if($item->siswa && $item->siswa->kelas)
                                {{ $item->siswa->kelas->nama_kelas }}
                            @else
                                <span style="color: red;">-</span>
                            @endif
                        </td>
                        <td>{{ $item->periode }}</td>
                        <td>Rp {{ number_format($item->jumlah_tagihan, 0, ',', '.') }}</td>
                        <td>
                            @php
                                $statusLabels = [
                                    'belum_bayar' => 'Belum Bayar',
                                    'pending' => 'Pending',
                                    'lunas' => 'Lunas',
                                    'gagal' => 'Gagal'
                                ];
                                $paymentStatus = $item->payment_status ?? $item->status;
                                $badgeColor = $paymentStatus == 'lunas' ? 'bg-success' : ($paymentStatus == 'pending' ? 'bg-info' : 'bg-warning');
                            @endphp
                            <span class="badge {{ $badgeColor }}">
                                {{ $statusLabels[$paymentStatus] ?? ucfirst(str_replace('_', ' ', $paymentStatus)) }}
                            </span>
                            @if($item->payment_date)
                                <br><small class="text-muted">{{ $item->payment_date->format('d/m/Y') }}</small>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('tagihan.show', $item->id_tagihan) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i> Lihat
                            </a>
                            <a href="{{ route('tagihan.edit', $item->id_tagihan) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <form action="{{ route('tagihan.destroy', $item->id_tagihan) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-danger" data-delete-btn data-item-name="tagihan ini">
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
