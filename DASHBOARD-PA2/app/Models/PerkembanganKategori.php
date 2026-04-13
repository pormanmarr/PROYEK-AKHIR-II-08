<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerkembanganKategori extends Model
{
    protected $table = 'perkembangan_kategori';
    protected $primaryKey = 'id_perkembangan_kategori';
    protected $fillable = [
        'id_perkembangan',
        'nama_kategori',
        'nilai',
        'status_utama',
        'deskripsi',
    ];

    public function perkembangan(): BelongsTo
    {
        return $this->belongsTo(Perkembangan::class, 'id_perkembangan', 'id_perkembangan');
    }
}
