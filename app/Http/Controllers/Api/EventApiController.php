<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::query()->where('status', '!=', 'pending_approval');

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $selesai = (clone $query)->where(function($q) {
            $q->where('status', 'selesai')
              ->orWhereDate('tanggal', '<', now());
        })->orderByDesc('tanggal')->get();
        
        $featured  = $selesai->where('unggulan', true)->values();
        
        $mendatang = (clone $query)->where('status', '!=', 'selesai')
                                   ->whereDate('tanggal', '>=', now())
                                   ->orderBy('tanggal')->get();

        $mapFoto = fn($e) => array_merge($e->toArray(), [
            'foto' => $e->foto ? asset('storage/' . $e->foto) : ($e->foto_pengaju ? asset('storage/' . $e->foto_pengaju) : null),
            'foto_pengaju' => $e->foto_pengaju ? asset('storage/' . $e->foto_pengaju) : null,
        ]);

        return response()->json([
            'featured'  => $featured->map($mapFoto)->values(),
            'selesai'   => $selesai->map($mapFoto)->values(),
            'mendatang' => $mendatang->map($mapFoto)->values(),
            'stats'     => [
                'total'          => Event::where('status', '!=', 'pending_approval')->count(),
                'internasional'  => Event::where('level', 'Internasional')->count(),
                'nasional_lokal' => Event::whereIn('level', ['Nasional', 'Lokal'])->count(),
                'penghargaan'    => Event::whereNotNull('hasil')->where('hasil', '!=', '')->count(),
            ],
        ]);
    }

    public function show($id)
    {
        $event = Event::findOrFail($id);

        return response()->json([
            'data' => array_merge($event->toArray(), [
                'foto' => $event->foto ? asset('storage/' . $event->foto) : ($event->foto_pengaju ? asset('storage/' . $event->foto_pengaju) : null),
                'foto_pengaju' => $event->foto_pengaju ? asset('storage/' . $event->foto_pengaju) : null,
            ]),
        ]);
    }
}
