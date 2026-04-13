@extends('layouts.app')

@section('title', 'Data Pembayaran')

@section('content')
<div class="row mb-3">
    <div class="col-md-8">
        <h2><i class="bi bi-credit-card"></i> Data Pembayaran</h2>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('pembayaran.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Pembayaran
        </a>
    </div>
</div>

@if ($pembayaran->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> Tidak ada data pembayaran
    </div>
@else
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Tagihan</th>
                        <th>Jumlah Bayar</th>
                        <th>Tgl Pembayaran</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pembayaran as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>#{{ $item->tagihan->id_tagihan }}</td>
                        <td>Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</td>
                        <td>{{ $item->tgl_pembayaran->format('d-m-Y') }}</td>
                        <td>
                            @php
                                $statusLabels = [
                                    'diterima' => 'Diterima',
                                    'menunggu' => 'Menunggu',
                                    'ditolak' => 'Ditolak'
                                ];
                            @endphp
                            <span class="badge 
                                @if($item->status_bayar == 'diterima') bg-success
                                @elseif($item->status_bayar == 'menunggu') bg-warning
                                @else bg-danger
                                @endif">
                                {{ $statusLabels[$item->status_bayar] ?? ucfirst($item->status_bayar) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('pembayaran.show', $item->id_pembayaran) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i> Lihat
                            </a>
                            <a href="{{ route('pembayaran.edit', $item->id_pembayaran) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <form action="{{ route('pembayaran.destroy', $item->id_pembayaran) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-danger" data-delete-btn data-item-name="pembayaran ini">
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
