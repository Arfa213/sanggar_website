<?php
// app/Models/PendaftaranTari.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PendaftaranTari extends Model
{
    use HasFactory;

    protected $table    = 'pendaftaran_tari';

    protected $fillable = [
        'user_id',
        'tarian_id',
        'jadwal_id',
        'tanggal_latihan',
        'jam_latihan',
        'status',
        'tanggal_daftar',
        'catatan',
    ];

    protected $casts = [
        'tanggal_daftar' => 'date',
    ];

    // ── Relasi ────────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tarian()
    {
        return $this->belongsTo(Tarian::class, 'tarian_id');
    }

    public function jadwal()
    {
        return $this->belongsTo(JadwalLatihan::class, 'jadwal_id');
    }

    public function kehadiran()
    {
        return $this->hasMany(Kehadiran::class, 'user_id', 'user_id')
                    ->where('jadwal_id', $this->jadwal_id)
                    ->where('tarian_id', $this->tarian_id);
    }

    // ── Scope: hanya yang aktif ───────────────────────────────
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
}