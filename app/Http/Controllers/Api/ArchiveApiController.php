<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tarian;
use App\Models\Topeng;
use Illuminate\Http\Request;

class ArchiveApiController extends Controller
{
    public function index()
    {
        $tarian = Tarian::where('aktif', true)->orderBy('urutan')->get();
        $topeng = Topeng::all();

        $tarianGrouped = $tarian->groupBy('jenis_kegiatan')->map(function ($items) {
            return $items->map(fn($t) => [
                'id' => $t->id,
                'nama' => $t->nama,
                'asal' => $t->asal,
                'kategori' => $t->kategori,
                'deskripsi' => $t->deskripsi,
                'fungsi' => $t->fungsi,
                'kostum' => $t->kostum,
                'durasi' => $t->durasi,
                'foto' => $t->foto ? asset('storage/' . $t->foto) : null,
                'video_url' => $t->video_url,
                'youtube_embed_url' => $t->youtube_embed_url,
                'unggulan' => $t->unggulan,
            ]);
        });

        $topengFormatted = $topeng->map(fn($t) => [
            'id' => $t->id,
            'nama' => $t->nama,
            'warna' => $t->warna,
            'karakter' => $t->karakter,
            'filosofi' => $t->filosofi,
            'deskripsi' => $t->deskripsi,
            'foto' => $t->foto ? asset('storage/' . $t->foto) : null,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'tarian' => $tarianGrouped,
                'topeng' => $topengFormatted,
            ]
        ]);
    }
}
