<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisPelanggaran extends Model
{
    protected $fillable = [
        'kode_pelanggaran',
        'nama_pelanggaran',
        'aspek_pelanggaran',
        'tingkat_pelanggaran',
        'poin',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'poin' => 'integer',
        ];
    }

    public function pelanggarans(): HasMany
    {
        return $this->hasMany(Pelanggaran::class);
    }
}