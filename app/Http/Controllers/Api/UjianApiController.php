<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\UjianPendaftaran;
use App\Models\Kehadiran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UjianApiController extends Controller
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
            return response()->json([
                'success' => false,
                'message' => 'Hanya anggota tetap yang dapat mendaftar Ujian Midhang Sore.'
            ], 403);
        }

        // 2. Cek Event Ujian
        $event = Event::find($request->event_id);
        if ($event->kategori !== 'midhang_sore' || $event->status === 'selesai') {
            return response()->json([
                'success' => false,
                'message' => 'Pendaftaran ujian hanya dibuka untuk event Midhang Sore yang akan datang.'
            ], 400);
        }

        // 3. Cek Kehadiran
        $totalSesi = Kehadiran::where('user_id', $user->id)->count();
        $totalHadir = Kehadiran::where('user_id', $user->id)->where('status', 'hadir')->count();
        $persenKehadiran = $totalSesi > 0 ? round(($totalHadir / $totalSesi) * 100, 2) : 0;

        if ($persenKehadiran < 75) {
            return response()->json([
                'success' => false,
                'message' => "Kehadiran Anda saat ini baru {$persenKehadiran}%. Minimal kehadiran adalah 75% untuk mendaftar."
            ], 422);
        }

        // 4. Cek double pendaftaran
        $exists = UjianPendaftaran::where([
            'user_id'   => $user->id,
            'event_id'  => $event->id,
            'tarian_id' => $request->tarian_id,
        ])->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah mendaftar ujian tari ini.'
            ], 422);
        }

        // 5. Simpan
        $pendaftaran = UjianPendaftaran::create([
            'user_id'          => $user->id,
            'event_id'         => $event->id,
            'tarian_id'        => $request->tarian_id,
            'persen_kehadiran' => $persenKehadiran,
            'status'           => 'menunggu',
            'catatan'          => $request->catatan,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran Ujian Midhang Sore berhasil diajukan.',
            'data'    => $pendaftaran
        ], 201);
    }

    public function statusSaya(Request $request)
    {
        $user = Auth::user();
        
        $data = UjianPendaftaran::with(['event', 'tarian'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Map data to also include rapor / score if available
        $result = $data->map(function ($item) use ($user) {
            $rapor = \App\Models\RaporPagelaran::where([
                'event_id'  => $item->event_id,
                'user_id'   => $user->id,
                'tarian_id' => $item->tarian_id
            ])->first();
            
            return [
                'id' => $item->id,
                'event' => [
                    'id' => $item->event->id,
                    'nama' => $item->event->nama,
                    'tanggal' => $item->event->tanggal,
                ],
                'tarian' => [
                    'id' => $item->tarian->id,
                    'nama' => $item->tarian->nama,
                ],
                'status' => $item->status,
                'persen_kehadiran' => $item->persen_kehadiran,
                'catatan' => $item->catatan,
                'catatan_admin' => $item->catatan_admin,
                'nilai' => $rapor ? [
                    'rapor_id' => $rapor->id,
                    'nilai_teknik' => $rapor->nilai_teknik,
                    'nilai_hafalan' => $rapor->nilai_hafalan,
                    'nilai_ekspresi' => $rapor->nilai_ekspresi,
                    'nilai_penampilan' => $rapor->nilai_penampilan,
                    'nilai_kehadiran' => $rapor->nilai_kehadiran,
                    'nilai_akhir' => $rapor->nilai_akhir,
                    'predikat' => $rapor->predikat,
                    'lulus' => (bool)$rapor->lulus,
                    'sertifikat_url' => $rapor->lulus ? route('ujian.sertifikat', $rapor->id) : null,
                ] : null
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }
}
