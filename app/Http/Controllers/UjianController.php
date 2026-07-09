<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Tarian;
use App\Models\UjianPendaftaran;
use App\Models\RaporPagelaran;
use App\Models\Kehadiran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class UjianController extends Controller
{
    public function daftar(Request $request)
    {
        $request->validate([
            'event_id'  => 'required|exists:events,id',
            'tarian_id' => 'required|exists:tarian,id',
            'catatan'   => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        // 1. Harus anggota tetap
        if ($user->tipe_anggota !== 'anggota_tetap') {
            return back()->with('error', 'Hanya anggota tetap yang dapat mendaftar Ujian Midhang Sore.');
        }

        // 2. Cek Event Ujian (Midhang Sore & status akan datang)
        $event = Event::findOrFail($request->event_id);
        if ($event->kategori !== 'midhang_sore' || $event->status === 'selesai') {
            return back()->with('error', 'Pendaftaran ujian hanya dibuka untuk event Midhang Sore yang akan datang.');
        }

        // 3. Hitung presentase kehadiran anggota
        $totalSesi = Kehadiran::where('user_id', $user->id)->count();
        $totalHadir = Kehadiran::where('user_id', $user->id)->where('status', 'hadir')->count();
        $persenKehadiran = $totalSesi > 0 ? round(($totalHadir / $totalSesi) * 100, 2) : 0;

        // Gate check 75% kehadiran
        if ($persenKehadiran < 75) {
            return back()->with('error', "Kehadiran Anda saat ini baru {$persenKehadiran}%. Minimal kehadiran adalah 75% untuk dapat mendaftar ujian.");
        }

        // 4. Cek double pendaftaran
        $exists = UjianPendaftaran::where([
            'user_id'   => $user->id,
            'event_id'  => $event->id,
            'tarian_id' => $request->tarian_id,
        ])->exists();

        if ($exists) {
            return back()->with('error', 'Anda sudah mendaftar ujian tari ini untuk event tersebut.');
        }

        // 5. Simpan Pendaftaran
        UjianPendaftaran::create([
            'user_id'          => $user->id,
            'event_id'         => $event->id,
            'tarian_id'        => $request->tarian_id,
            'persen_kehadiran' => $persenKehadiran,
            'status'           => 'menunggu',
            'catatan'          => $request->catatan,
        ]);

        return back()->with('success', 'Pendaftaran Ujian Midhang Sore berhasil dikirim! Menunggu konfirmasi dari admin.');
    }

    public function downloadSertifikat($raporId)
    {
        $user = Auth::user();
        
        // Cari Rapor kelulusan di rapor_pagelaran
        $rapor = RaporPagelaran::with(['event', 'tarian', 'user'])
            ->where('id', $raporId)
            ->where('user_id', $user->id)
            ->where('lulus', true)
            ->firstOrFail();

        $pdf = Pdf::loadView('ujian.sertifikat', compact('rapor'))
            ->setPaper('a4', 'landscape');
            
        return $pdf->download("Sertifikat_Ujian_{$rapor->tarian->nama}_{$user->name}.pdf");
    }
}
