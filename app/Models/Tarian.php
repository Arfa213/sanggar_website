<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tarian extends Model {
    use HasFactory;
    protected $table = 'tarian';
    protected $fillable = [
        'nama','asal','kategori','jenis_kegiatan','deskripsi','fungsi',
        'kostum','durasi','foto','video_url','unggulan','urutan','aktif',
    ];
    protected $casts = [
        'unggulan' => 'boolean',
        'aktif'    => 'boolean',
    ];
    public function scopeAktif($q)   { return $q->where('aktif', true)->orderBy('urutan'); }
    public function scopeUnggulan($q){ return $q->where('unggulan', true); }

    public function getYoutubeEmbedUrlAttribute(): ?string {
        if (!$this->video_url) return null;

        $url = $this->video_url;

        // Already embed URL
        if (str_contains($url, 'embed/')) return $url;

        // youtube.com/watch?v=xxx
        if (str_contains($url, 'watch?v=')) {
            preg_match('/[?&]v=([^&]+)/', $url, $matches);
            if (isset($matches[1])) {
                return 'https://www.youtube.com/embed/' . $matches[1];
            }
        }

        // youtu.be/xxx
        if (str_contains($url, 'youtu.be/')) {
            preg_match('/youtu\.be\/([^\?]+)/', $url, $matches);
            if (isset($matches[1])) {
                return 'https://www.youtube.com/embed/' . $matches[1];
            }
        }

        return $url;
    }

public function jadwalLatihan()
{
    return $this->belongsTo(JadwalLatihan::class, 'jadwal_id');
}
public function pendaftaran()
{
    return $this->hasMany(\App\Models\PendaftaranTari::class);
}
public function kehadiran()
{
    return $this->hasMany(\App\Models\Kehadiran::class);
}
}
