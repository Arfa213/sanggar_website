<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UjianPendaftaran extends Model
{
    use HasFactory;

    protected $table = 'ujian_pendaftaran';

    protected $fillable = [
        'user_id',
        'event_id',
        'tarian_id',
        'status',
        'persen_kehadiran',
        'catatan',
        'catatan_admin',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function tarian()
    {
        return $this->belongsTo(Tarian::class, 'tarian_id');
    }
}
