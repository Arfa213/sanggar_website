<?php
// app/Http/Controllers/Admin/KehadiranController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Kehadiran, JadwalLatihan, Tarian, PendaftaranTari, User, SesiKehadiran};
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KehadiranController extends Controller
{
    // ── Halaman utama: pilih sesi + barcode ─────────────────────
    public function index()
    {
        $jadwal  = JadwalLatihan::where('aktif', true)->orderBy('urutan')->get();
        $tarian  = Tarian::where('aktif', true)->orderBy('urutan')->get();
        $today   = now()->format('Y-m-d');

        // Statistik hari ini
        $statsHariIni = Kehadiran::whereDate('tanggal', $today)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Sesi aktif hari ini (barcode)
        $sesiAktif = SesiKehadiran::with(['jadwal', 'tarian'])
            ->whereDate('tanggal', $today)
            ->where('aktif', true)
            ->get();

        // Sesi yang sudah diinput manual hari ini
        $sesiHariIni = Kehadiran::with(['jadwal', 'tarian'])
            ->whereDate('tanggal', $today)
            ->select('jadwal_id', 'tarian_id')
            ->distinct()
            ->get();

        return view('admin.kehadiran.index', compact(
            'jadwal', 'tarian', 'statsHariIni', 'today', 'sesiHariIni', 'sesiAktif'
        ));
    }

    // ── Buat sesi barcode ─────────────────────────────────────────
    public function buatSesi(Request $request)
    {
        $request->validate([
            'jadwal_id' => 'required|exists:jadwal_latihan,id',
            'tarian_id' => 'required|exists:tarian,id',
            'tanggal'   => 'required|date',
            'durasi'    => 'nullable|integer|min:15|max:480',
        ]);

        $token = Str::random(40);
        $durasi = $request->durasi ?? 120; // default 2 jam

        $sesi = SesiKehadiran::create([
            'jadwal_id'    => $request->jadwal_id,
            'tarian_id'    => $request->tarian_id,
            'tanggal'      => $request->tanggal,
            'barcode_token' => $token,
            'aktif'        => true,
            'expires_at'   => now()->addMinutes($durasi),
            'dibuat_oleh'  => auth()->user()->name,
        ]);

        return redirect()->route('admin.kehadiran.sesi', $sesi->id)
            ->with('success', 'Sesi kehadiran barcode berhasil dibuat!');
    }

    // ── Tampilkan QR Code sesi ────────────────────────────────────
    public function tampilSesi($id)
    {
        $sesi = SesiKehadiran::with(['jadwal', 'tarian'])->findOrFail($id);
        $scanUrl = route('kehadiran.scan', $sesi->barcode_token);
        return view('admin.kehadiran.sesi', compact('sesi', 'scanUrl'));
    }

    // ── Tutup / nonaktifkan sesi ──────────────────────────────────
    public function tutupSesi($id)
    {
        $sesi = SesiKehadiran::findOrFail($id);
        $sesi->update(['aktif' => false]);
        return redirect()->route('admin.kehadiran.index')
            ->with('success', 'Sesi kehadiran telah ditutup.');
    }

    // ── Input kehadiran per jadwal + tanggal (manual) ─────────────
    public function inputKehadiran(Request $request)
    {
        $request->validate([
            'jadwal_id' => 'required|exists:jadwal_latihan,id',
            'tarian_id' => 'required|exists:tarian,id',
            'tanggal'   => 'required|date',
        ]);

        // Anggota yang terdaftar di jadwal + tarian ini
        $peserta = PendaftaranTari::with('user')
            ->where('jadwal_id', $request->jadwal_id)
            ->where('tarian_id', $request->tarian_id)
            ->where('status', 'aktif')
            ->get();

        // Kehadiran yang sudah diinput untuk sesi ini
        $existing = Kehadiran::where('jadwal_id', $request->jadwal_id)
            ->where('tarian_id', $request->tarian_id)
            ->whereDate('tanggal', $request->tanggal)
            ->pluck('status', 'user_id');

        $keteranganExisting = Kehadiran::where('jadwal_id', $request->jadwal_id)
            ->where('tarian_id', $request->tarian_id)
            ->whereDate('tanggal', $request->tanggal)
            ->pluck('keterangan', 'user_id');

        $jadwal = JadwalLatihan::findOrFail($request->jadwal_id);
        $tarian = Tarian::findOrFail($request->tarian_id);

        return view('admin.kehadiran.input', compact(
            'peserta', 'existing', 'keteranganExisting', 'jadwal', 'tarian', 'request'
        ));
    }

    // ── Simpan kehadiran manual ───────────────────────────────────
    public function simpanKehadiran(Request $request)
    {
        $request->validate([
            'jadwal_id'    => 'required|exists:jadwal_latihan,id',
            'tarian_id'    => 'required|exists:tarian,id',
            'tanggal'      => 'required|date',
            'kehadiran'    => 'required|array',
            'kehadiran.*'  => 'required|in:hadir,izin,alpa',
            'keterangan'   => 'nullable|array',
        ]);

        foreach ($request->kehadiran as $userId => $status) {
            Kehadiran::updateOrCreate(
                [
                    'user_id'   => $userId,
                    'jadwal_id' => $request->jadwal_id,
                    'tarian_id' => $request->tarian_id,
                    'tanggal'   => $request->tanggal,
                ],
                [
                    'status'        => $status,
                    'keterangan'    => $request->keterangan[$userId] ?? null,
                    'dicatat_oleh'  => auth()->user()->name,
                    'metode_absen'  => 'manual',
                ]
            );
        }

        return redirect()->route('admin.kehadiran.index')
            ->with('success', 'Kehadiran berhasil disimpan untuk '
                . count($request->kehadiran) . ' peserta!');
    }

    // ── Anggota scan barcode (public) ─────────────────────────────
    public function scanBarcode($token)
    {
        $sesi = SesiKehadiran::with(['jadwal', 'tarian'])
            ->where('barcode_token', $token)
            ->first();

        if (!$sesi) {
            return view('kehadiran.scan-result', ['success' => false, 'pesan' => 'Kode tidak valid.']);
        }
        if (!$sesi->isValid()) {
            return view('kehadiran.scan-result', ['success' => false, 'pesan' => 'Sesi kehadiran sudah berakhir atau tidak aktif.', 'sesi' => $sesi]);
        }

        return view('kehadiran.scan-form', compact('sesi', 'token'));
    }

    // ── Proses scan barcode anggota ───────────────────────────────
    public function prosesBarcode(Request $request, $token)
    {
        $sesi = SesiKehadiran::with(['jadwal', 'tarian'])
            ->where('barcode_token', $token)
            ->firstOrFail();

        if (!$sesi->isValid()) {
            return back()->with('error', 'Sesi sudah berakhir!');
        }

        $request->validate(['user_id' => 'required|exists:users,id']);

        $user = User::findOrFail($request->user_id);

        // Cek apakah user terdaftar di sesi ini
        $terdaftar = PendaftaranTari::where('user_id', $user->id)
            ->where('jadwal_id', $sesi->jadwal_id)
            ->where('tarian_id', $sesi->tarian_id)
            ->where('status', 'aktif')
            ->exists();

        if (!$terdaftar) {
            return view('kehadiran.scan-result', [
                'success' => false,
                'pesan'   => 'Kamu tidak terdaftar di kelas ini.',
                'sesi'    => $sesi,
            ]);
        }

        // Cek sudah hadir atau belum
        $sudahHadir = Kehadiran::where('user_id', $user->id)
            ->where('jadwal_id', $sesi->jadwal_id)
            ->where('tarian_id', $sesi->tarian_id)
            ->whereDate('tanggal', $sesi->tanggal)
            ->exists();

        if ($sudahHadir) {
            return view('kehadiran.scan-result', [
                'success' => false,
                'pesan'   => 'Kamu sudah tercatat hadir di sesi ini.',
                'sesi'    => $sesi,
                'user'    => $user,
            ]);
        }

        Kehadiran::create([
            'user_id'        => $user->id,
            'jadwal_id'      => $sesi->jadwal_id,
            'tarian_id'      => $sesi->tarian_id,
            'tanggal'        => $sesi->tanggal,
            'status'         => 'hadir',
            'keterangan'     => 'Scan barcode otomatis',
            'dicatat_oleh'   => 'Sistem (Barcode)',
            'barcode_token'  => $token,
            'scan_at'        => now(),
            'metode_absen'   => 'barcode',
        ]);

        return view('kehadiran.scan-result', [
            'success' => true,
            'pesan'   => 'Kehadiran berhasil dicatat!',
            'sesi'    => $sesi,
            'user'    => $user,
        ]);
    }

    // ── Laporan rekap kehadiran ───────────────────────────────────
    public function laporan(Request $request)
    {
        $jadwal_id  = $request->jadwal_id;
        $tarian_id  = $request->tarian_id;
        $bulan      = $request->bulan ?? now()->format('Y-m');

        $query = Kehadiran::with(['user', 'jadwal', 'tarian'])
            ->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$bulan]);

        if ($jadwal_id) $query->where('jadwal_id', $jadwal_id);
        if ($tarian_id) $query->where('tarian_id', $tarian_id);

        $kehadiran  = $query->orderBy('tanggal')->paginate(50);
        $jadwalList = JadwalLatihan::where('aktif', true)->get();
        $tarianList = Tarian::where('aktif', true)->get();

        // Rekap per anggota
        $rekap = $query->get()
            ->groupBy('user_id')
            ->map(function ($items) {
                $total = $items->count();
                return [
                    'user'   => $items->first()->user,
                    'hadir'  => $items->where('status', 'hadir')->count(),
                    'izin'   => $items->where('status', 'izin')->count(),
                    'alpa'   => $items->where('status', 'alpa')->count(),
                    'total'  => $total,
                    'persen' => $total > 0
                        ? round($items->where('status', 'hadir')->count() / $total * 100)
                        : 0,
                ];
            })->values();

        return view('admin.kehadiran.laporan',
            compact('kehadiran', 'rekap', 'jadwalList', 'tarianList', 'bulan', 'jadwal_id', 'tarian_id'));
    }
}