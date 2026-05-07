<?php

namespace App\Http\Controllers;

use App\Models\{PendaftaranTari, Kehadiran, Event, Tarian, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    // Tidak ada __construct middleware — sudah dihandle di routes/web.php

    public function index()
    {
        $user = Auth::user();

        // Jadwal aktif saya
        $jadwalAktif = PendaftaranTari::with(['tarian', 'jadwal'])
            ->where('user_id', $user->id)
            ->where('status', 'aktif')
            ->get();

        // Kehadiran bulan ini
        $bulanIni = now()->format('Y-m');
        $kehadiranBulanIni = Kehadiran::where('user_id', $user->id)
            ->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$bulanIni])
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $totalLatihan = array_sum($kehadiranBulanIni);
        $hadir        = (int)($kehadiranBulanIni['hadir'] ?? 0);
        $izin         = (int)($kehadiranBulanIni['izin']  ?? 0);
        $alpa         = (int)($kehadiranBulanIni['alpa']  ?? 0);
        $persenHadir  = $totalLatihan > 0 ? round($hadir / $totalLatihan * 100) : 0;

        // Event mendatang
        $eventMendatang = Event::where('status', 'akan_datang')
            ->orderBy('tanggal')->limit(3)->get();

        // Rekomendasi tarian (yang belum didaftar)
        $tarianRekomendasi = Tarian::where('aktif', true)
            ->whereNotIn('id', $jadwalAktif->pluck('tarian_id'))
            ->orderBy('urutan')->limit(4)->get();

        // Absensi terakhir 5 sesi
        $absensiTerakhir = Kehadiran::with(['jadwal', 'tarian'])
            ->where('user_id', $user->id)
            ->orderByDesc('tanggal')->limit(5)->get();

        // Total kehadiran sepanjang waktu
        $totalKehadiranAll = Kehadiran::where('user_id', $user->id)->count();
        $totalHadirAll     = Kehadiran::where('user_id', $user->id)->where('status', 'hadir')->count();

        return view('pages.dashboard', compact(
            'user', 'jadwalAktif', 'kehadiranBulanIni',
            'totalLatihan', 'hadir', 'izin', 'alpa', 'persenHadir',
            'eventMendatang', 'tarianRekomendasi', 'absensiTerakhir',
            'totalKehadiranAll', 'totalHadirAll'
        ));
    }

    public function editProfile()
    {
        $user = Auth::user();
        return view('pages.member_profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email,' . $user->id,
            'no_hp'    => 'nullable|string|max:20',
            'alamat'   => 'nullable|string|max:500',
            'foto'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = $request->only(['name', 'email', 'no_hp', 'alamat']);

        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }
            $data['foto'] = $request->file('foto')->store('profil_anggota', 'public');
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Gunakan model binding atau instance dari DB untuk memastikan update berhasil
        User::where('id', $user->id)->update($data);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}
