<?php

namespace App\Http\Controllers;

use App\Models\Pengunjung;
use Illuminate\Http\Request;

class TamuController extends Controller
{
    public function index()
    {
        return view('pages.tamu.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:255',
            'no_hp'    => 'required|string|max:20',
            'instansi' => 'nullable|string|max:255',
            'tujuan'   => 'required|string|max:500',
        ], [
            'nama.required'   => 'Nama wajib diisi.',
            'no_hp.required'  => 'Nomor HP wajib diisi.',
            'tujuan.required' => 'Tujuan kunjungan wajib diisi.',
        ]);

        Pengunjung::create([
            'nama'     => $request->nama,
            'no_hp'    => $request->no_hp,
            'instansi' => $request->instansi,
            'tujuan'   => $request->tujuan,
            'tanggal'  => now()->toDateString(),
            'jam'      => now()->toTimeString(),
        ]);

        return back()->with('success', 'Data kunjungan Anda telah berhasil dicatat. Terima kasih!');
    }
}
