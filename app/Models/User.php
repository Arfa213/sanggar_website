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
        'nomor_induk',
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
    
    // ─── BOOT ──────────────────────────────────────
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            // Jika user adalah anggota dan nomor_induk belum diset
            if ($user->role === 'anggota' && empty($user->nomor_induk)) {
                $user->nomor_induk = self::generateNomorInduk();
            }
        });
    }

    public static function generateNomorInduk(): string
    {
        $datePrefix = now()->format('ymd'); // format: YYMMDD
        
        // Cari user terakhir yang didaftarkan pada hari yang sama
        $lastUser = self::where('nomor_induk', 'like', $datePrefix . '%')
                        ->orderBy('nomor_induk', 'desc')
                        ->first();
                        
        if ($lastUser && !empty($lastUser->nomor_induk)) {
            // Ambil 3 digit terakhir
            $lastSequence = (int) substr($lastUser->nomor_induk, -3);
            $newSequence = $lastSequence + 1;
        } else {
            $newSequence = 1;
        }
        
        return $datePrefix . str_pad($newSequence, 3, '0', STR_PAD_LEFT);
    }

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

    public function raporPagelaran()
    {
        return $this->hasMany(\App\Models\RaporPagelaran::class);
    }

    public function ujianPendaftaran()
    {
        return $this->hasMany(\App\Models\UjianPendaftaran::class);
    }

    public function scopeAnggotaVerified($query)
    {
        return $query->where('role', 'anggota')
            ->where(function($q) {
                $q->whereNotNull('email_verified_at')
                  ->orWhere('created_at', '<', '2026-05-21');
            });
    }

    // ─── RELATIONS ─────────────────────────────────
    // Uncomment saat model terkait sudah dibuat
    // public function kegiatan() { return $this->belongsToMany(Kegiatan::class); }
}