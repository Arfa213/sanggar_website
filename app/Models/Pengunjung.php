<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengunjung extends Model
{
    use HasFactory;

    protected $table = 'pengunjung';

    protected $fillable = [
        'nama',
        'no_hp',
        'tujuan',
        'tanggal',
        'jam',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];
}
