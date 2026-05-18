<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tarian;
use Illuminate\Http\Request;

class TarianApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Tarian::where('aktif', true)->orderBy('urutan');

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $tarian = $query->get()->map(fn($t) => array_merge($t->toArray(), [
            'foto' => $t->foto ? asset('storage/' . $t->foto) : null,
            'youtube_embed_url' => $t->youtube_embed_url,
        ]));

        return response()->json(['data' => $tarian]);
    }

    public function show($id)
    {
        $tarian = Tarian::findOrFail($id);

        return response()->json([
            'data' => array_merge($tarian->toArray(), [
                'foto' => $tarian->foto ? asset('storage/' . $tarian->foto) : null,
                'youtube_embed_url' => $tarian->youtube_embed_url,
            ]),
        ]);
    }
}
