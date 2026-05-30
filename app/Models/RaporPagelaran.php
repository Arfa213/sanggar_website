<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RaporPagelaran extends Model
{
    use HasFactory;

    protected $table = 'rapor_pagelaran';

    protected $fillable = [
        'event_id',
        'user_id',
        'tarian_id',
        'pelatih_id',
        'nilai_teknik',
        'nilai_hafalan',
        'nilai_ekspresi',
        'nilai_penampilan',
        'nilai_kehadiran',
        'nilai_akhir',
        'predikat',
        'lulus',
        'catatan',
        'notif_terkirim'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tarian()
    {
        return $this->belongsTo(Tarian::class);
    }

    public function pelatih()
    {
        return $this->belongsTo(User::class, 'pelatih_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($rapor) {
            $rapor->nilai_akhir = round(
                ($rapor->nilai_teknik * 0.25) +
                ($rapor->nilai_hafalan * 0.25) +
                ($rapor->nilai_ekspresi * 0.20) +
                ($rapor->nilai_penampilan * 0.20) +
                ($rapor->nilai_kehadiran * 0.10),
                2
            );

            if ($rapor->nilai_akhir >= 90) {
                $rapor->predikat = 'Istimewa';
            } elseif ($rapor->nilai_akhir >= 80) {
                $rapor->predikat = 'Sangat Baik';
            } elseif ($rapor->nilai_akhir >= 70) {
                $rapor->predikat = 'Baik';
            } elseif ($rapor->nilai_akhir >= 60) {
                $rapor->predikat = 'Cukup';
            } else {
                $rapor->predikat = 'Perlu Peningkatan';
            }
            
            $rapor->lulus = $rapor->nilai_akhir >= 60;
        });
    }
}
