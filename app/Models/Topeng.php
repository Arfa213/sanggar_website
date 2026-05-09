<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topeng extends Model
{
    use HasFactory;

    protected $table = 'topeng';

    protected $fillable = [
        'nama',
        'warna',
        'karakter',
        'filosofi',
        'deskripsi',
        'foto',
        'urutan',
        'aktif',
    ];

    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }
}
