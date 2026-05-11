<?php
// app/Models/SesiKehadiran.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SesiKehadiran extends Model
{
    use HasFactory;

    protected $table = 'sesi_kehadiran';

    protected $fillable = [
        'jadwal_id',
        'tarian_id',
        'tanggal',
        'barcode_token',
        'aktif',
        'expires_at',
        'dibuat_oleh',
    ];

    protected $casts = [
        'tanggal'    => 'date',
        'expires_at' => 'datetime',
        'aktif'      => 'boolean',
    ];

    public function jadwal()
    {
        return $this->belongsTo(JadwalLatihan::class, 'jadwal_id');
    }

    public function tarian()
    {
        return $this->belongsTo(Tarian::class, 'tarian_id');
    }

    public function kehadiran()
    {
        return $this->hasMany(Kehadiran::class, 'barcode_token', 'barcode_token');
    }

    public function isValid(): bool
    {
        if (!$this->aktif) return false;
        if ($this->expires_at && now()->isAfter($this->expires_at)) return false;
        return true;
    }
}
