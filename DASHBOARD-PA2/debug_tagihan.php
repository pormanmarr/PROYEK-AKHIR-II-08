<?php

use Illuminate\Support\Facades\DB;

// Check if there's a mismatch between tagihan nomor_induk_siswa and siswa data
$tagihanWithoutSiswa = DB::table('tagihan')
    ->leftJoin('siswa', 'tagihan.nomor_induk_siswa', '=', 'siswa.nomor_induk_siswa')
    ->whereNull('siswa.nomor_induk_siswa')
    ->select('tagihan.id_tagihan', 'tagihan.nomor_induk_siswa', 'tagihan.periode')
    ->get();

echo "Tagihan tanpa siswa: " . count($tagihanWithoutSiswa) . "\n";
foreach($tagihanWithoutSiswa as $t) {
    echo "- ID: {$t->id_tagihan}, NIS: '{$t->nomor_induk_siswa}'\n";
}

// Check siswa count
$siswaCount = DB::table('siswa')->count();
echo "\nTotal siswa: $siswaCount\n";

// Check first 3 siswa
$siswaList = DB::table('siswa')->limit(3)->get();
foreach($siswaList as $s) {
    echo "- NIS: '{$s->nomor_induk_siswa}', Nama: {$s->nama_siswa}\n";
}
?>
