@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Buat Tagihan Massal</h4>
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

                    <form action="{{ route('tagihan.bulkCreateStore') }}" method="POST">
                        @csrf

                        {{-- Tipe Target --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Target Pembuat Tagihan</label>
                            <div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tipe_target" id="tipe_semua" 
                                           value="semua_siswa" checked onchange="updatePreview()">
                                    <label class="form-check-label" for="tipe_semua">
                                        Semua Siswa
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tipe_target" id="tipe_kelas"
                                           value="per_kelas" onchange="updatePreview()">
                                    <label class="form-check-label" for="tipe_kelas">
                                        Per Kelas
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Pilih Kelas --}}
                        <div class="mb-3" id="kelasDiv" style="display: none;">
                            <label for="id_kelas" class="form-label fw-bold">Pilih Kelas</label>
                            <select name="id_kelas" id="id_kelas" class="form-select @error('id_kelas') is-invalid @enderror"
                                    onchange="updatePreview()">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach ($kelas as $k)
                                    <option value="{{ $k->id_kelas }}">{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                            @error('id_kelas')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Jumlah Tagihan --}}
                        <div class="mb-3">
                            <label for="jumlah_tagihan" class="form-label fw-bold">Jumlah Tagihan (Rp)</label>
                            <input type="number" name="jumlah_tagihan" id="jumlah_tagihan" class="form-control @error('jumlah_tagihan') is-invalid @enderror"
                                   value="{{ old('jumlah_tagihan') }}" placeholder="Contoh: 250000" min="1" onchange="updatePreview()">
                            @error('jumlah_tagihan')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Periode --}}
                        <div class="mb-3">
                            <label for="periode" class="form-label fw-bold">Periode Tagihan</label>
                            <input type="text" name="periode" id="periode" class="form-control @error('periode') is-invalid @enderror"
                                   value="{{ old('periode', 'SPP Bulan April 2026') }}" placeholder="SPP Bulan April 2026">
                            @error('periode')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Preview Section --}}
                        <div class="alert alert-info" id="previewAlert" style="display: none;">
                            <strong>Preview:</strong><br>
                            Akan membuat <strong id="previewCount">0</strong> tagihan untuk
                            <strong id="previewTarget">semua siswa</strong>
                            <br><small class="text-muted">Duplikat tagihan dengan periode yang sama akan dilewati</small>
                        </div>

                        {{-- Buttons --}}
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle"></i> Buat Tagihan Massal
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

    function updatePreview() {
        const tipeTarget = document.querySelector('input[name="tipe_target"]:checked').value;
        const kelasDivElement = document.getElementById('kelasDiv');
        const kelasInputElement = document.getElementById('id_kelas');
        const previewAlert = document.getElementById('previewAlert');
        const previewCount = document.getElementById('previewCount');
        const previewTarget = document.getElementById('previewTarget');

        // Tampilkan/sembunyikan pilihan kelas & reset jika hidden
        if (tipeTarget === 'per_kelas') {
            kelasDivElement.style.display = 'block';
        } else {
            kelasDivElement.style.display = 'none';
            kelasInputElement.value = ''; // Reset saat switch ke "semua siswa"
        }

        // Hitung jumlah siswa
        let count = 0;
        let targetText = '';

        if (tipeTarget === 'semua_siswa') {
            count = {{ $kelas->sum(fn($k) => $k->siswa->count()) }};
            targetText = 'semua siswa';
        } else {
            const selectedKelas = kelasInputElement.value;
            count = selectedKelas ? (kelasData[selectedKelas] || 0) : 0;
            const selectedText = document.querySelector('#id_kelas option:checked')?.textContent || 'kelas';
            targetText = selectedText;
        }

        // Update preview
        previewCount.textContent = count;
        previewTarget.textContent = targetText;
        previewAlert.style.display = count > 0 ? 'block' : 'none';
    }

    // Initialize preview
    document.addEventListener('DOMContentLoaded', updatePreview);
</script>
@endsection
