<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluasiModel extends Model
{
    use HasFactory;

    protected $table = 'evaluasi_models';

    protected $fillable = [
        'metode',
        'jumlah_data_training',
        'jumlah_data_testing',
        'akurasi',
        'precision',
        'recall',
        'f1_score',
        'confusion_matrix',
        'keterangan'
    ];
}