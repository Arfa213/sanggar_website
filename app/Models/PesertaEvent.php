<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PesertaEvent extends Model
{
    use HasFactory;

    protected $table = 'peserta_event';

    protected $fillable = [
        'event_id',
        'order_id',
        'nama_peserta',
        'no_hp',
        'asal_instansi',
        'status_pembayaran',
        'bukti_transfer',
        'snap_token',
        'catatan_admin'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
