<?php

namespace App\Http\Controllers;

use App\Models\{PendaftaranTari, Kehadiran, Event, Tarian, User, UjianPendaftaran, RaporPagelaran};
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

        // Admin diarahkan ke dashboard admin
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // Jika anggota sementara (pengunjung)
        if ($user->tipe_anggota === 'pengunjung') {
            return $this->guestDashboard($user);
        }

        // Jadwal aktif saya (untuk anggota tetap)
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

        // Ujian Midhang Sore saya (untuk anggota tetap)
        $ujianSaya = UjianPendaftaran::with(['event', 'tarian'])
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($item) use ($user) {
                $item->rapor = RaporPagelaran::where([
                    'event_id'  => $item->event_id,
                    'user_id'   => $user->id,
                    'tarian_id' => $item->tarian_id,
                ])->first();
                return $item;
            });

        return view('pages.dashboard', compact(
            'user', 'jadwalAktif', 'kehadiranBulanIni',
            'totalLatihan', 'hadir', 'izin', 'alpa', 'persenHadir',
            'eventMendatang', 'tarianRekomendasi', 'absensiTerakhir',
            'totalKehadiranAll', 'totalHadirAll', 'ujianSaya'
        ));
    }

    private function guestDashboard($user)
    {
        // Semua sesi pendaftaran (termasuk yang pending)
        $sesiBooking = PendaftaranTari::with('tarian')
            ->where('user_id', $user->id)
            ->orderBy('tanggal_latihan', 'asc')
            ->get();

        // Hitung statistik
        $totalSesiBooking = $sesiBooking->count();
        $totalHadir       = Kehadiran::where('user_id', $user->id)->where('status', 'hadir')->count();
        $persenHadir      = $totalSesiBooking > 0 ? round(($totalHadir / $totalSesiBooking) * 100) : 0;

        // Event mendatang
        $eventMendatang = Event::where('status', 'akan_datang')->orderBy('tanggal')->limit(3)->get();

        // Daftar tarian aktif untuk form tambah sesi
        $tarianList = Tarian::where('aktif', true)->orderBy('urutan')->get();

        return view('pages.guest_dashboard', compact(
            'user', 'sesiBooking', 'totalSesiBooking', 'totalHadir', 'persenHadir',
            'eventMendatang', 'tarianList'
        ));
    }

    // ─────────────────────────────────────────
    //  TAMBAH SESI BARU (Anggota Sementara)
    // ─────────────────────────────────────────
    public function storeSesi(Request $request)
    {
        $user = Auth::user();

        if ($user->tipe_anggota !== 'pengunjung') {
            return redirect()->route('dashboard');
        }

        $request->validate([
            'tarian_id' => 'required|exists:tarian,id',
            'tanggal'   => 'required|date|after_or_equal:today',
            'jam'       => 'required|string',
        ], [
            'tarian_id.required' => 'Harap pilih tarian.',
            'tanggal.required'   => 'Harap pilih tanggal.',
            'tanggal.after_or_equal' => 'Tanggal tidak boleh di masa lalu.',
            'jam.required'       => 'Harap pilih jam latihan.',
        ]);

        $requestedTarianId = $request->tarian_id;

        // 1. Dapatkan daftar pendaftaran aktif/pending di jam tersebut
        $existingPendaftaran = PendaftaranTari::with('tarian')
            ->where('tanggal_latihan', $request->tanggal)
            ->where('jam_latihan', $request->jam)
            ->whereIn('status', ['aktif', 'pending'])
            ->get();

        // 2. Hitung jumlah jenis tarian yang unik (Maksimal 2 tempat)
        $uniqueTarianIds = $existingPendaftaran->pluck('tarian_id')->unique()->values()->toArray();
        
        // 3. Cek kapasitas tempat/ruangan
        if (!in_array($requestedTarianId, $uniqueTarianIds)) {
            if (count($uniqueTarianIds) >= 2) {
                $namaTarian = $existingPendaftaran->pluck('tarian.nama')->unique()->implode(' dan ');
                return back()->with('error', "Maaf, seluruh tempat latihan pada {$request->tanggal} jam {$request->jam} sudah penuh oleh kelas {$namaTarian}. Silakan pilih jam lain, atau pilih salah satu tarian tersebut jika ingin bergabung.");
            }
        }

        // 4. Cek kapasitas orang dalam kelompok tarian yang dipilih (Maksimal 5 orang)
        $countOrangDiTarian = $existingPendaftaran->where('tarian_id', $requestedTarianId)->count();
        if ($countOrangDiTarian >= 5) {
            return back()->with('error', "Maaf, kelompok tari yang Anda pilih pada {$request->tanggal} jam {$request->jam} sudah mencapai batas maksimal (5 orang). Silakan pilih jam lain.");
        }

        // Update kadaluarsa jika sesi baru lebih jauh
        $tglBaru = \Carbon\Carbon::parse($request->tanggal)->addDays(3)->toDateString();
        if (is_null($user->tgl_kadaluarsa) || $tglBaru > $user->tgl_kadaluarsa) {
            User::where('id', $user->id)->update(['tgl_kadaluarsa' => $tglBaru]);
        }

        PendaftaranTari::create([
            'user_id'         => $user->id,
            'tarian_id'       => $request->tarian_id,
            'tanggal_latihan' => $request->tanggal,
            'jam_latihan'     => $request->jam,
            'status'          => 'pending',
            'tanggal_daftar'  => now()->toDateString(),
            'catatan'         => null,
        ]);

        return back()->with('success', 'Sesi latihan baru berhasil diajukan! Menunggu konfirmasi admin.');
    }


    public function editProfile()
    {
        $user = Auth::user();

        // Admin diarahkan ke dashboard admin
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // Anggota sementara tidak diizinkan edit profil (permintaan user)
        if ($user->tipe_anggota === 'pengunjung') {
            return redirect()->route('dashboard')->with('error', 'Anggota sementara tidak memerlukan pengaturan profil.');
        }

        $riwayatTarian = PendaftaranTari::with(['tarian', 'jadwal'])
            ->where('user_id', $user->id)
            ->orderByDesc('tanggal_daftar')
            ->get();

        return view('pages.member_profile', compact('user', 'riwayatTarian'));
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
