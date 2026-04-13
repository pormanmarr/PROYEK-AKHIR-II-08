@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0"><i class="bi bi-arrow-repeat"></i> Update Status Tagihan Massal</h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Validasi Gagal!</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('tagihan.bulkUpdateStatusStore') }}" method="POST" id="bulkUpdateForm">
                        @csrf

                        <h5 class="mb-3 text-muted">Filter Tagihan yang Akan Diupdate</h5>

                        {{-- Filter Kelas --}}
                        <div class="mb-3">
                            <label for="filter_id_kelas" class="form-label">Kelas (Opsional)</label>
                            <select name="filter_id_kelas" id="filter_id_kelas" class="form-select" onchange="updatePreview()">
                                <option value="">-- Semua Kelas --</option>
                                @foreach ($kelas as $k)
                                    <option value="{{ $k->id_kelas }}">{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Filter Periode --}}
                        <div class="mb-3">
                            <label for="filter_periode" class="form-label">Periode (Opsional)</label>
                            <select name="filter_periode" id="filter_periode" class="form-select" onchange="updatePreview()">
                                <option value="">-- Semua Periode --</option>
                                @foreach ($periode as $p)
                                    <option value="{{ $p }}">{{ $p }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Filter Status --}}
                        <div class="mb-3">
                            <label for="filter_status" class="form-label">Status Current (Opsional)</label>
                            <select name="filter_status" id="filter_status" class="form-select" onchange="updatePreview()">
                                <option value="">-- Semua Status --</option>
                                @foreach ($statuses as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <hr>

                        {{-- New Status --}}
                        <div class="mb-3">
                            <label for="new_status" class="form-label fw-bold">Status Baru</label>
                            <select name="new_status" id="new_status" class="form-select @error('new_status') is-invalid @enderror" required onchange="updatePreview()">
                                <option value="">-- Pilih Status Baru --</option>
                                @foreach ($statuses as $key => $label)
                                    <option value="{{ $key }}" @if (old('new_status') == $key) selected @endif>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('new_status')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Preview --}}
                        <div class="alert alert-info" id="previewAlert" style="display: none;">
                            <strong>Preview:</strong><br>
                            <span id="previewText">Tidak ada filter yang dipilih</span>
                        </div>

                        {{-- Warning jika ada yang perlu diperhatikan --}}
                        <div class="alert alert-warning" id="warningAlert" style="display: none;">
                            <i class="bi bi-exclamation-triangle"></i>
                            <strong>Perhatian!</strong> Pastikan filter yang dipilih sudah benar sebelum apply.
                        </div>

                        {{-- Buttons --}}
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning" id="submitBtn" disabled>
                                <i class="bi bi-check-circle"></i> Apply Update
                            </button>
                            <a href="{{ route('tagihan.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Data untuk preview
    const kelasData = {!! json_encode($kelas->mapWithKeys(function($k) {
        return [$k->id_kelas => $k->siswa->count()];
    })->toArray()) !!};

    const statusLabels = {
        'belum_bayar': 'Belum Bayar',
        'lunas': 'Lunas'
    };

    function updatePreview() {
        const filterKelas = document.getElementById('filter_id_kelas').value;
        const filterPeriode = document.getElementById('filter_periode').value;
        const filterStatus = document.getElementById('filter_status').value;
        const newStatus = document.getElementById('new_status').value;
        const previewAlert = document.getElementById('previewAlert');
        const warningAlert = document.getElementById('warningAlert');
        const previewText = document.getElementById('previewText');
        const submitBtn = document.getElementById('submitBtn');

        // Build preview text
        let filters = [];
        if (filterKelas) {
            const kelasName = document.querySelector(`#filter_id_kelas option[value="${filterKelas}"]`)?.textContent || 'Kelas';
            filters.push(`Kelas: <strong>${kelasName}</strong>`);
        }
        if (filterPeriode) {
            filters.push(`Periode: <strong>${filterPeriode}</strong>`);
        }
        if (filterStatus) {
            filters.push(`Status: <strong>${statusLabels[filterStatus]}</strong>`);
        }

        let previewHtml = '';
        if (newStatus) {
            previewHtml = `Akan mengubah status menjadi <strong>${statusLabels[newStatus]}</strong> untuk `;
            if (filters.length > 0) {
                previewHtml += `tagihan dengan: ${filters.join(', ')}`;
            } else {
                previewHtml += `<strong>SEMUA tagihan</strong>`;
            }
        } else {
            previewHtml = 'Pilih status baru terlebih dahulu';
        }

        previewText.innerHTML = previewHtml;
        previewAlert.style.display = newStatus ? 'block' : 'none';
        warningAlert.style.display = newStatus ? 'block' : 'none';
        submitBtn.disabled = !newStatus;
    }

    // Initialize preview
    document.addEventListener('DOMContentLoaded', updatePreview);
</script>
@endsection
