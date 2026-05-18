<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pengunjung;
use Illuminate\Http\Request;

class GuestApiController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:255',
            'no_hp'    => 'required|string|max:20',
            'instansi' => 'nullable|string|max:255',
            'tujuan'   => 'required|string|max:500',
        ]);

        $guest = Pengunjung::create([
            'nama'     => $request->nama,
            'no_hp'    => $request->no_hp,
            'instansi' => $request->instansi,
            'tujuan'   => $request->tujuan,
            'tanggal'  => now()->toDateString(),
            'jam'      => now()->toTimeString(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data kunjungan berhasil dicatat.',
            'data'    => $guest
        ], 201);
    }
}
