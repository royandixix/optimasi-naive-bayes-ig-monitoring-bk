<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JenisPelanggaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_jenis',
        'nama_jenis',
        'poin',
        'keterangan'
    ];

    public function pelanggarans()
    {
        return $this->hasMany(Pelanggaran::class);
    }
}