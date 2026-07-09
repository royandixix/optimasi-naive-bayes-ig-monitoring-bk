<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswas';

   protected $fillable = [
    'nis',
    'nama',
    'jk',
    'kelas_id',
    'tempat_lahir',
    'tanggal_lahir',
    'alamat',
    'nama_ayah',
    'nama_ibu',
    'no_hp_ortu',
    'status'
];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function pelanggaran()
    {
        return $this->hasMany(Pelanggaran::class);
    }

    public function klasifikasi()
    {
        return $this->hasOne(Klasifikasi::class);
    }
}