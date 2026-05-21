<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable {
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'google_id',
        'alamat',
        'no_hp',
        'foto',
        'password',
        'role',
        'status',
        'tipe_anggota',
        'tgl_kadaluarsa',
        'catatan_keanggotaan',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'tgl_kadaluarsa'    => 'date',
    ];
    

    // ─── HELPERS ───────────────────────────────────
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }

    public function getTipeAnggotaLabelAttribute(): string
    {
        return match ($this->tipe_anggota ?? 'anggota_tetap') {
            'anggota_tetap' => 'Anggota Tetap',
            'pengunjung'    => 'Anggota Sementara',
            'private'       => 'Private',
            default         => 'Anggota Tetap',
        };
    }

    public function getTipeAnggotaColorAttribute(): string
    {
        return match ($this->tipe_anggota ?? 'anggota_tetap') {
            'anggota_tetap' => 'chip--green',
            'pengunjung'    => 'chip--purple',
            'private'       => 'chip--blue',
            default         => 'chip--gray',
        };
    }
   public function pendaftaranTari()
{
    return $this->hasMany(\App\Models\PendaftaranTari::class);
}

public function kehadiran()
{
    return $this->hasMany(\App\Models\Kehadiran::class);
}

    // ─── RELATIONS ─────────────────────────────────
    // Uncomment saat model terkait sudah dibuat
    // public function kegiatan() { return $this->belongsToMany(Kegiatan::class); }
}