<?php

namespace App\Http\Controllers;

use App\Models\{Kehadiran, Tarian, PendaftaranTari, User};
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{
    public function guestIndex()
    {
        return view('pages.attendance.guest');
    }

    public function guestStore(Request $request)
    {
        $request->validate([
            'nama'            => 'required|string|max:100',
            'no_hp'           => 'required|string|max:20',
            'tarian_id'       => 'required|exists:tarian,id',
            'tanggal_latihan' => 'required|date|after_or_equal:today',
            'jam_latihan'     => 'required',
            'tujuan'          => 'nullable|string|max:500',
        ]);

        // Cek Aula (Kapasitas 2)
        $tanggal = $request->tanggal_latihan;
        $jam     = $request->jam_latihan;
        $tarianId = $request->tarian_id;

        // Block Friday Afternoon and Sunday for Public (Reserved for Regulars)
        $dayOfWeek = date('N', strtotime($tanggal)); // 1 (Mon) to 7 (Sun)
        if ($dayOfWeek == 5 && (int)explode(':', $jam)[0] >= 13) {
            return back()->with('error', 'Maaf, hari Jumat siang aula dipesan untuk latihan rutin anggota tetap.');
        }
        if ($dayOfWeek == 7) {
            return back()->with('error', 'Maaf, hari Minggu aula dipesan penuh untuk latihan rutin anggota tetap.');
        }

        $sessionsAtTime = PendaftaranTari::where('tanggal_latihan', $tanggal)
            ->where('jam_latihan', $jam)
            ->where('status', 'aktif')
            ->get();

        $distinctDances = $sessionsAtTime->pluck('tarian_id')->unique();

        if ($distinctDances->count() >= 2 && !$distinctDances->contains($tarianId)) {
            return back()->with('error', 'Maaf, pada jam tersebut aula sudah penuh. Silakan pilih jam atau hari lain.');
        }

        // Simpan ke Pendaftaran (sebagai pengunjung)
        // Kita buat dummy user atau gunakan field nama/no_hp jika tabel mendukung?
        // Tabel pendaftaran_tari saat ini mewajibkan user_id. 
        // Solusi: Cari user 'pengunjung' atau buat user baru otomatis?
        
        // Agar rapi, kita cari apakah ada user dengan no_hp ini, jika tidak buat akun 'pengunjung'
        $user = User::where('no_hp', $request->no_hp)->first();
        if (!$user) {
            $user = User::create([
                'name'         => $request->nama,
                'email'        => 'guest_' . Str::random(8) . '@sanggar.com',
                'no_hp'        => $request->no_hp,
                'password'     => bcrypt('guest123'),
                'role'         => 'member',
                'tipe_anggota' => 'pengunjung'
            ]);
        }

        PendaftaranTari::create([
            'user_id'         => $user->id,
            'tarian_id'       => $tarianId,
            'tanggal_latihan' => $tanggal,
            'jam_latihan'     => $jam,
            'status'          => 'aktif',
            'tanggal_daftar'  => now()->toDateString(),
            'catatan'         => $request->tujuan,
        ]);

        return back()->with('success', "Booking latihan berhasil untuk {$request->nama} pada tanggal {$tanggal} jam {$jam}. Sampai jumpa di sanggar!");
    }
}
