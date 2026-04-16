<?php
/**
 * Script untuk update template_deskripsi untuk semua records yang sudah ada
 * Jalankan: php artisan tinker kemudian paste isi file ini
 * Atau: php update_template_deskripsi.php
 */

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Perkembangan;

$templates = [
    'BB' => 'Anak belum menunjukkan kemampuan dalam aspek ini. Perlu dukungan dan bimbingan intensif dari guru untuk mengembangkan kompetensi ini.',
    'MB' => 'Anak mulai menunjukkan kemampuan dalam aspek ini namun masih memerlukan bimbingan. Perlu terus didukung untuk mencapai perkembangan yang lebih baik.',
    'BSH' => 'Anak menunjukkan kemampuan yang sesuai dengan harapan untuk usia/tingkatannya. Anak mampu melaksanakan tugas dengan cukup baik.',
    'BSB' => 'Anak menunjukkan kemampuan yang sangat menonjol dalam aspek ini. Anak mampu melaksanakan tugas dengan sangat baik dan melampaui harapan.'
];

$perkembangans = Perkembangan::whereNull('template_deskripsi')->get();

foreach ($perkembangans as $p) {
    $p->template_deskripsi = $templates[$p->status_utama] ?? '';
    $p->save();
    echo "Updated ID: " . $p->id_perkembangan . " - Status: " . $p->status_utama . PHP_EOL;
}

echo "Total records updated: " . count($perkembangans) . PHP_EOL;
