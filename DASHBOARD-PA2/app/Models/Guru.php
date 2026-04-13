<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guru extends Model
{
    protected $table = 'guru';
    protected $primaryKey = 'id_guru';
    protected $keyType = 'int';
    public $incrementing = true;
    protected $fillable = ['nama_guru', 'no_hp', 'email'];

    public function kelas(): HasMany
    {
        return $this->hasMany(Kelas::class, 'id_guru', 'id_guru');
    }

    public function akun(): HasMany
    {
        return $this->hasMany(Akun::class, 'id_guru', 'id_guru');
    }

    public function pengumuman(): HasMany
    {
        return $this->hasMany(Pengumuman::class, 'id_guru', 'id_guru');
    }

    public function perkembangan(): HasMany
    {
        return $this->hasMany(Perkembangan::class, 'id_guru', 'id_guru');
    }
}
