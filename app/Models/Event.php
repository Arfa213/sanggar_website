<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model {
    use HasFactory;
    protected $table = 'events';
    protected $fillable = [
        'nama','lokasi','tanggal','kategori','level','hasil',
        'deskripsi','foto','penghargaan','jumlah_penonton','unggulan','status',
        'is_external','nama_pengaju','no_hp_pengaju','portofolio_link','catatan_pengaju',
        'is_berbayar','harga_tiket','foto_pengaju','sinopsis_link'
    ];
    protected $casts = [
        'tanggal'     => 'date',
        'penghargaan' => 'array',
        'unggulan'    => 'boolean',
        'is_external' => 'boolean',
        'is_berbayar' => 'boolean',
    ];

    public function peserta() {
        return $this->hasMany(PesertaEvent::class);
    }

    public function scopeSelesai($q)    { return $q->where('status','selesai')->orderByDesc('tanggal'); }
    public function scopeMendatang($q)  { return $q->where('status','akan_datang')->orderBy('tanggal'); }
    public function scopePending($q)    { return $q->where('status','pending_approval')->orderBy('created_at'); }
    public function scopeUnggulan($q)   { return $q->where('unggulan', true); }

    public function getTahunAttribute(): string { return $this->tanggal->format('Y'); }
    public function getBulanAttribute(): string { return $this->tanggal->isoFormat('MMM'); }
    public function getTglAttribute(): string   { return $this->tanggal->format('d'); }
}