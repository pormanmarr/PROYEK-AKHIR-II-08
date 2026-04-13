<?php

namespace Database\Seeders;

use App\Models\Tagihan;
use App\Models\Siswa;
use Illuminate\Database\Seeder;

class TagihanSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua siswa
        $siswaList = Siswa::all();

        if ($siswaList->isEmpty()) {
            return;
        }

        // Periode yang berbeda
        $periode = [
            'SPP Bulan April 2026',
            'SPP Bulan May 2026',
            'SPP Bulan June 2026',
        ];

        // Buat tagihan untuk setiap siswa
        foreach ($siswaList as $siswa) {
            foreach ($periode as $p) {
                Tagihan::create([
                    'nomor_induk_siswa' => $siswa->nomor_induk_siswa,
                    'jumlah_tagihan' => 200000,
                    'periode' => $p,
                    'status' => 'belum_bayar',
                ]);
            }
        }
    }
}
