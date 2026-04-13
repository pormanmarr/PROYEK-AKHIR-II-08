<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Perkembangan extends Model
{
    protected $table = 'perkembangan';
    protected $primaryKey = 'id_perkembangan';
    protected $fillable = [
        'id_guru',
        'nomor_induk_siswa',
        'bulan',
        'tahun',
        'kategori',
        'deskripsi',
        'status_utama'
    ];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id_guru');
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'nomor_induk_siswa', 'nomor_induk_siswa');
    }

    public function kategoriDetails(): HasMany
    {
        return $this->hasMany(PerkembanganKategori::class, 'id_perkembangan', 'id_perkembangan');
    }
}
