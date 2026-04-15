<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$db = $app->make('db');

$siswa = $db->table('siswa')->select('nomor_induk_siswa', 'nama_siswa')->orderBy('nomor_induk_siswa')->get();
echo "Total Siswa: " . count($siswa) . "\n";
foreach ($siswa as $s) {
    echo "nomor_induk_siswa: {$s->nomor_induk_siswa}, nama: {$s->nama_siswa}\n";
}
