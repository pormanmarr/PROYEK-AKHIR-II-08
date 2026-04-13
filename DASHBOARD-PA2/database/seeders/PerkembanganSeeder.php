<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Perkembangan;
use App\Models\Siswa;

class PerkembanganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first 2 siswa to create sample perkembangan
        $siswaList = Siswa::limit(2)->get();

        if ($siswaList->isEmpty()) {
            return;
        }

        $templates = [
            [
                'bulan' => 3,
                'tahun' => 2026,
                'kategori' => 'Kognitif',
                'deskripsi' => 'Anak dapat mengenali angka 1-10 dengan baik. Perkembangan kognitif sudah sangat baik, terutama dalam hal pengenalan warna, bentuk, dan ukuran. Anak juga sudah mampu memecahkan puzzle sederhana dengan lancar.',
                'status_utama' => 'BSB'
            ],
            [
                'bulan' => 3,
                'tahun' => 2026,
                'kategori' => 'Akademik',
                'deskripsi' => 'Kemampuan membaca anak sudah berkembang sesuai harapan. Dapat membaca beberapa kata sederhana dan memahami cerita pendek. Keterampilan menulis juga menunjukkan perkembangan yang baik.',
                'status_utama' => 'BSH'
            ],
        ];

        // Create perkembangan for each siswa
        foreach ($siswaList as $siswa) {
            foreach ($templates as $template) {
                Perkembangan::create([
                    'id_guru' => $siswa->kelas->id_guru ?? 1,
                    'nomor_induk_siswa' => $siswa->nomor_induk_siswa,
                    'bulan' => $template['bulan'],
                    'tahun' => $template['tahun'],
                    'kategori' => $template['kategori'],
                    'deskripsi' => $template['deskripsi'],
                    'status_utama' => $template['status_utama'],
                ]);
            }
        }
    }
}
