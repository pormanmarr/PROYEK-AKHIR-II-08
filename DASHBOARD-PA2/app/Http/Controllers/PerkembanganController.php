<?php

namespace App\Http\Controllers;

use App\Models\Perkembangan;
use App\Models\PerkembanganKategori;
use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PerkembanganController extends Controller
{
    public function index()
    {
        // Semua guru (termasuk superadmin) bisa melihat SEMUA perkembangan anak
        $perkembangan = Perkembangan::with('guru')
            ->with('siswa.kelas')
            ->orderBy('id_perkembangan', 'desc')
            ->get();
        
        return view('perkembangan.index', compact('perkembangan'));
    }

    public function create()
    {
        // Pastikan user adalah guru dan punya id_guru
        if (!session('id_guru')) {
            return redirect()->route('perkembangan.index')->with('error', 
                'Akun Anda tidak terhubung dengan data guru. ' .
                'Hubungi super admin untuk link akun Anda dengan guru.'
            );
        }

        // Tampilkan SEMUA siswa yang ada
        $siswa = Siswa::with('kelas')->get();

        return view('perkembangan.create', compact('siswa'));
    }

    public function store(Request $request)
    {
        if (!session('id_guru')) {
            return redirect()->route('perkembangan.index')->with('error', 
                'Anda tidak berwenang menambah perkembangan. Hanya guru yang dapat menambah perkembangan.'
            );
        }

        // Validate kategori dan status_utama - harus semua 3 kategori
        $request->validate([
            'kategori' => 'required|array|size:3',
            'status_utama' => 'required|in:BB,MB,BSH,BSB'
        ]);
        
        $kategoris = $request->input('kategori', []);
        $statusUtama = $request->input('status_utama');

        // Build validation rules for nilai dynamically
        $rules = ['nomor_induk_siswa' => 'required|exists:siswa,nomor_induk_siswa'];
        foreach ($kategoris as $kategori) {
            $keyLower = strtolower($kategori);
            $rules['nilai_' . $keyLower] = 'required|integer|between:1,10';
        }

        $validated = $request->validate($rules);

        // Create main perkembangan record
        $perkembangan = Perkembangan::create([
            'id_guru' => session('id_guru'),
            'nomor_induk_siswa' => $validated['nomor_induk_siswa'],
            'bulan' => Carbon::now()->month,
            'tahun' => Carbon::now()->year,
            'kategori' => implode(',', $kategoris),
            'status_utama' => $statusUtama,
            'deskripsi' => $request->input('deskripsi_tambahan') ?? ''
        ]);

        // Create kategori details for each selected kategori
        foreach ($kategoris as $kategori) {
            $keyLower = strtolower($kategori);
            $nilai = $validated['nilai_' . $keyLower];
            
            // Generate description based on status_utama and nilai
            $deskripsi = $this->generateDeskripsi($statusUtama, $nilai, $kategori);
            
            PerkembanganKategori::create([
                'id_perkembangan' => $perkembangan->id_perkembangan,
                'nama_kategori' => $kategori,
                'nilai' => $nilai,
                'status_utama' => $statusUtama,
                'deskripsi' => $deskripsi,
            ]);
        }

        return redirect()->route('perkembangan.index')->with('success', 'Perkembangan berhasil ditambahkan');
    }

    private function generateDeskripsi($status, $nilai, $kategori)
    {
        $statusDescriptions = [
            'BB' => 'Belum berkembang',
            'MB' => 'Mulai berkembang',
            'BSH' => 'Berkembang sesuai harapan',
            'BSB' => 'Berkembang sangat baik'
        ];
        
        $nilaiDescriptions = [
            1 => 'sangat rendah', 2 => 'rendah', 3 => 'rendah-sedang', 4 => 'sedang-rendah', 5 => 'sedang',
            6 => 'sedang-tinggi', 7 => 'tinggi-sedang', 8 => 'tinggi', 9 => 'sangat tinggi', 10 => 'sempurna'
        ];
        
        $statusDesc = $statusDescriptions[$status] ?? 'Tidak diketahui';
        $nilaiDesc = $nilaiDescriptions[$nilai] ?? 'Tidak diketahui';
        
        return "Perkembangan {$kategori} anak menunjukkan status {$statusDesc} dengan nilai {$nilai}/10 ({$nilaiDesc}). Anak perlu mendapatkan dukungan berkelanjutan untuk mencapai perkembangan yang lebih optimal.";
    }

    public function show(Perkembangan $perkembangan)
    {
        $perkembangan->load('kategoriDetails');
        return view('perkembangan.show', compact('perkembangan'));
    }

    public function edit(Perkembangan $perkembangan)
    {
        // Super admin bisa edit semua, regular guru bisa edit:
        // 1. Perkembangan untuk siswa di kelasnya
        // 2. Perkembangan yang mereka buat sendiri
        if (!session('is_super_admin')) {
            $kelasGuruArray = Kelas::where('id_guru', session('id_guru'))->pluck('id_kelas')->toArray();
            $siswaGuruArray = !empty($kelasGuruArray) ? Siswa::whereIn('id_kelas', $kelasGuruArray)->pluck('nomor_induk_siswa')->toArray() : [];
            
            $isOwnCreation = $perkembangan->id_guru == session('id_guru');
            $isKelasStudent = in_array($perkembangan->nomor_induk_siswa, $siswaGuruArray);
            
            if (!$isOwnCreation && !$isKelasStudent) {
                return redirect()->route('perkembangan.index')->with('error', 
                    'Anda tidak berwenang mengedit perkembangan ini.'
                );
            }
        }

        // Tampilkan SEMUA siswa yang ada
        $siswa = Siswa::with('kelas')->get();
        
        // Load kategori details with eager loading
        $perkembangan->load('kategoriDetails');
        
        // Format selected categories array
        $selectedCategories = $perkembangan->kategoriDetails->pluck('nama_kategori')->toArray();
        
        // Create map for quick lookup
        $kategoriMap = [];
        foreach ($perkembangan->kategoriDetails as $detail) {
            $kategoriMap[$detail->nama_kategori] = [
                'nilai' => $detail->nilai,
                'deskripsi' => $detail->deskripsi
            ];
        }

        return view('perkembangan.edit', compact('perkembangan', 'siswa', 'selectedCategories', 'kategoriMap'));
    }

    public function update(Request $request, Perkembangan $perkembangan)
    {
        // Super admin bisa edit semua, regular guru bisa edit:
        // 1. Perkembangan untuk siswa di kelasnya
        // 2. Perkembangan yang mereka buat sendiri
        if (!session('is_super_admin')) {
            $kelasGuruArray = Kelas::where('id_guru', session('id_guru'))->pluck('id_kelas')->toArray();
            $siswaGuruArray = !empty($kelasGuruArray) ? Siswa::whereIn('id_kelas', $kelasGuruArray)->pluck('nomor_induk_siswa')->toArray() : [];
            
            $isOwnCreation = $perkembangan->id_guru == session('id_guru');
            $isKelasStudent = in_array($perkembangan->nomor_induk_siswa, $siswaGuruArray);
            
            if (!$isOwnCreation && !$isKelasStudent) {
                return redirect()->route('perkembangan.index')->with('error', 
                    'Anda tidak berwenang mengedit perkembangan ini.'
                );
            }
        }

        // Validate kategori dan status_utama - harus semua 3 kategori
        $request->validate([
            'kategori' => 'required|array|size:3',
            'status_utama' => 'required|in:BB,MB,BSH,BSB'
        ]);
        
        $kategoris = $request->input('kategori', []);
        $statusUtama = $request->input('status_utama');

        // Build validation rules for nilai dynamically
        $rules = ['nomor_induk_siswa' => 'required|exists:siswa,nomor_induk_siswa'];
        foreach ($kategoris as $kategori) {
            $keyLower = strtolower($kategori);
            $rules['nilai_' . $keyLower] = 'required|integer|between:1,10';
        }

        $validated = $request->validate($rules);

        // Update main perkembangan record
        $perkembangan->update([
            'nomor_induk_siswa' => $validated['nomor_induk_siswa'],
            'kategori' => implode(',', $kategoris),
            'status_utama' => $statusUtama,
            'deskripsi' => $request->input('deskripsi_tambahan') ?? ''
        ]);

        // Delete old kategori details
        $perkembangan->kategoriDetails()->delete();

        // Create new kategori details for each selected kategori
        foreach ($kategoris as $kategori) {
            $keyLower = strtolower($kategori);
            $nilai = $validated['nilai_' . $keyLower];
            
            // Generate description based on status_utama and nilai
            $deskripsi = $this->generateDeskripsi($statusUtama, $nilai, $kategori);
            
            PerkembanganKategori::create([
                'id_perkembangan' => $perkembangan->id_perkembangan,
                'nama_kategori' => $kategori,
                'nilai' => $nilai,
                'status_utama' => $statusUtama,
                'deskripsi' => $deskripsi,
            ]);
        }

        return redirect()->route('perkembangan.index')->with('success', 'Perkembangan berhasil diperbarui');
    }

    public function destroy(Perkembangan $perkembangan)
    {
        // Super admin bisa hapus semua, regular guru hanya untuk:
        // 1. Perkembangan untuk siswa di kelasnya
        // 2. Perkembangan yang mereka buat sendiri
        if (!session('is_super_admin')) {
            $kelasGuruArray = Kelas::where('id_guru', session('id_guru'))->pluck('id_kelas')->toArray();
            $siswaGuruArray = !empty($kelasGuruArray) ? Siswa::whereIn('id_kelas', $kelasGuruArray)->pluck('nomor_induk_siswa')->toArray() : [];
            
            $isOwnCreation = $perkembangan->id_guru == session('id_guru');
            $isKelasStudent = in_array($perkembangan->nomor_induk_siswa, $siswaGuruArray);
            
            if (!$isOwnCreation && !$isKelasStudent) {
                return redirect()->route('perkembangan.index')->with('error', 
                    'Anda tidak berwenang menghapus perkembangan ini.'
                );
            }
        }

        $perkembangan->delete();
        return redirect()->route('perkembangan.index')->with('success', 'Perkembangan berhasil dihapus');
    }
}
