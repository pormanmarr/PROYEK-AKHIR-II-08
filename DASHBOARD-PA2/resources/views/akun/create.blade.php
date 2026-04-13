@extends('layouts.app')

@section('title', 'Generate Akun')

@section('content')
<div class="row">
    <div class="col-md-8">
        <h2><i class="bi bi-plus-circle"></i> Generate Akun Baru</h2>
    </div>
</div>

<div class="card mt-4">
    <div class="card-body">
        <form action="{{ route('akun.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                <select class="form-control @error('role') is-invalid @enderror" id="role" name="role" required onchange="updateOptions()">
                    <option value="">-- Pilih Role --</option>
                    <option value="guru" {{ old('role') == 'guru' ? 'selected' : '' }}>Guru</option>
                    <option value="orangtua" {{ old('role') == 'orangtua' ? 'selected' : '' }}>Orangtua</option>
                </select>
                @error('role')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3" id="super-admin-check" style="display:none;">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="is_super_admin" name="is_super_admin" value="1" {{ old('is_super_admin') ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_super_admin">
                        Set sebagai Super Admin (dapat mengelola data guru dan akun)
                    </label>
                </div>
            </div>

            <div class="mb-3" id="guru-select" style="display:none;">
                <label for="id_guru" class="form-label">Guru <span class="text-danger">*</span></label>
                <select class="form-control @error('id_guru') is-invalid @enderror" id="id_guru" name="id_guru">
                    <option value="">-- Pilih Guru --</option>
                    @foreach ($guru as $g)
                        <option value="{{ $g->id_guru }}" {{ old('id_guru') == $g->id_guru ? 'selected' : '' }}>
                            {{ $g->nama_guru }}
                        </option>
                    @endforeach
                </select>
                @error('id_guru')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3" id="siswa-select" style="display:none;">
                <label for="nomor_induk_siswa" class="form-label">Siswa <span class="text-danger">*</span></label>
                <select class="form-control @error('nomor_induk_siswa') is-invalid @enderror" id="nomor_induk_siswa" name="nomor_induk_siswa">
                    <option value="">-- Pilih Siswa --</option>
                    @foreach ($siswa as $s)
                        <option value="{{ $s->nomor_induk_siswa }}" {{ old('nomor_induk_siswa') == $s->nomor_induk_siswa ? 'selected' : '' }}>
                            {{ $s->nama_siswa }} ({{ $s->nama_orgtua }})
                        </option>
                    @endforeach
                </select>
                @error('nomor_induk_siswa')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Username akan dibuat otomatis berdasarkan nama Guru/Siswa. 
                    Jika ada duplikat, akan ditambahkan suffix (01, 02, dst)
                </div>
            </div>

            <div class="mb-3">
                <div class="alert alert-warning">
                    <i class="bi bi-key"></i> <strong>Password Default:</strong> Password akun baru akan otomatis di-generate dengan password <code>password123</code>. User dapat mengubah password setelah login.
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Simpan
                </button>
                <a href="{{ route('akun.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function updateOptions() {
    const role = document.getElementById('role').value;
    document.getElementById('guru-select').style.display = role === 'guru' ? 'block' : 'none';
    document.getElementById('siswa-select').style.display = role === 'orangtua' ? 'block' : 'none';
    document.getElementById('super-admin-check').style.display = role === 'guru' ? 'block' : 'none';
}
updateOptions();
</script>
@endsection
