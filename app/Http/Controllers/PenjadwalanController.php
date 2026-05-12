<?php

namespace App\Http\Controllers;

use App\Models\{Tarian, JadwalLatihan, PendaftaranTari, Kehadiran};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenjadwalanController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $pendaftaran = PendaftaranTari::with(['tarian', 'jadwal'])
            ->where('user_id', $user->id)
            ->where('status', 'aktif')
            ->orderByDesc('created_at')
            ->get();

        $tarianTersedia = Tarian::where('aktif', true)->orderBy('urutan')->get();
        
        // Tetap kirim jadwalLatihan untuk referensi, tapi kita gunakan dynamic booking
        $jadwalLatihan = JadwalLatihan::where('aktif', true)->orderBy('urutan')->get();

        return view('pages.penjadwalan', compact('pendaftaran', 'tarianTersedia', 'jadwalLatihan'));
    }

    public function daftar(Request $request)
    {
        $request->validate([
            'tarian_id'       => 'required|exists:tarian,id',
            'tanggal_latihan' => 'required|date|after_or_equal:today',
            'jam_latihan'     => 'required', // Format H:i
            'catatan'         => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $tarianId = $request->tarian_id;
        $tanggal = $request->tanggal_latihan;
        $jam = $request->jam_latihan;

        // 1. Cek apakah user sudah daftar tarian ini di jam yang sama
        $exists = PendaftaranTari::where([
            'user_id'         => $user->id,
            'tarian_id'       => $tarianId,
            'tanggal_latihan' => $tanggal,
            'jam_latihan'     => $jam,
            'status'          => 'aktif'
        ])->exists();

        if ($exists) {
            return back()->with('error', 'Kamu sudah terdaftar di sesi ini!');
        }

        // 2. Cek Kapasitas Aula (Maksimal 2 tarian berbeda per jam)
        $sessionsAtTime = PendaftaranTari::where('tanggal_latihan', $tanggal)
            ->where('jam_latihan', $jam)
            ->where('status', 'aktif')
            ->with('tarian')
            ->get();

        $distinctDances = $sessionsAtTime->pluck('tarian.nama', 'tarian_id')->unique();
        
        if ($distinctDances->count() >= 2) {
            // Jika sudah ada 2 tarian, cek apakah tarian yang dipilih termasuk salah satunya
            if (!$distinctDances->has($tarianId)) {
                $names = $distinctDances->implode(' dan ');
                return back()->with('error', "Maaf, aula sudah penuh pada jam ini oleh kelas: {$names}. Silakan pilih jam atau tanggal lain.");
            }
        }

        // 3. Simpan Pendaftaran
        PendaftaranTari::create([
            'user_id'         => $user->id,
            'tarian_id'       => $tarianId,
            'tanggal_latihan' => $tanggal,
            'jam_latihan'     => $jam,
            'status'          => 'aktif',
            'tanggal_daftar'  => now()->toDateString(),
            'catatan'         => $request->catatan,
        ]);

        $tarian = Tarian::find($tarianId);
        $msg = "Berhasil mendaftar Tari {$tarian->nama} untuk tanggal {$tanggal} jam {$jam}.";
        
        if ($distinctDances->has($tarianId)) {
            $msg .= " Kamu bergabung dengan grup yang sudah ada di aula.";
        } else if ($distinctDances->count() == 1) {
            $msg .= " Kamu akan menggunakan Aula ke-2 (Aula 1 sedang digunakan kelas {$distinctDances->first()}).";
        }

        return back()->with('success', $msg);
    }

    public function batalkan($id)
    {
        $pendaftaran = PendaftaranTari::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $pendaftaran->update(['status' => 'nonaktif']);
        return back()->with('success', 'Pendaftaran berhasil dibatalkan.');
    }

    public function riwayatKehadiran()
    {
        $kehadiran = Kehadiran::with(['jadwal', 'tarian'])
            ->where('user_id', Auth::id())
            ->orderByDesc('tanggal')
            ->paginate(20);

        return view('pages.riwayat_kehadiran', compact('kehadiran'));
    }
}
