<?php
// app/Models/Kehadiran.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kehadiran extends Model
{
    use HasFactory;

    protected $table    = 'kehadiran';

    protected $fillable = [
        'user_id',
        'jadwal_id',
        'tarian_id',
        'tanggal',
        'status',
        'keterangan',
        'dicatat_oleh',
        'barcode_token',
        'scan_at',
        'metode_absen',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // ── Relasi ────────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jadwal()
    {
        return $this->belongsTo(JadwalLatihan::class, 'jadwal_id');
    }

    public function tarian()
    {
        return $this->belongsTo(Tarian::class, 'tarian_id');
    }

    // ── Helper: label warna status ────────────────────────────
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'hadir' => '#2E7D32',
            'izin'  => '#E65100',
            'alpa'  => '#DC2626',
            default => '#6B7280',
        };
    }

    public function getStatusBgAttribute(): string
    {
        return match ($this->status) {
            'hadir' => '#E8F5E9',
            'izin'  => '#FFF3E0',
            'alpa'  => '#FEF2F2',
            default => '#F3F4F6',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'hadir' => '✓ Hadir',
            'izin'  => '~ Izin',
            'alpa'  => '✗ Alpa',
            default => $this->status,
        };
    }
}