<?php
// app/Http/Controllers/Admin/KehadiranController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Kehadiran, JadwalLatihan, Tarian, PendaftaranTari, User};
use Illuminate\Http\Request;

class KehadiranController extends Controller
{
    // ── Halaman utama: pilih jadwal ───────────────────────────
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
        // Sesi yang sudah diinput hari ini
        $sesiHariIni = Kehadiran::with(['jadwal', 'tarian'])
            ->whereDate('tanggal', $today)
            ->select('jadwal_id', 'tarian_id')
            ->distinct()
            ->get();

        return view('admin.kehadiran.index', compact('jadwal', 'tarian', 'statsHariIni', 'today', 'sesiHariIni'));
    }

    // ── Input kehadiran per jadwal + tanggal ──────────────────
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

    // ── Simpan kehadiran ──────────────────────────────────────
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
                    'status'       => $status,
                    'keterangan'   => $request->keterangan[$userId] ?? null,
                    'dicatat_oleh' => auth()->user()->name,
                ]
            );
        }

        return redirect()->route('admin.kehadiran.index')
            ->with('success', 'Kehadiran berhasil disimpan untuk '
                . count($request->kehadiran) . ' peserta!');
    }

    // ── Laporan rekap kehadiran ───────────────────────────────
    public function laporan(Request $request)
    {
        $jadwal_id  = $request->jadwal_id;
        $tarian_id  = $request->tarian_id;
        $bulan      = $request->bulan ?? now()->format('Y-m');

        $query = Kehadiran::with(['user', 'jadwal', 'tarian'])
            ->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$bulan]);

        if ($jadwal_id) $query->where('jadwal_id', $jadwal_id);
        if ($tarian_id) $query->where('tarian_id', $tarian_id);

        $kehadiran = $query->orderBy('tanggal')->paginate(50);
        $jadwalList    = JadwalLatihan::where('aktif', true)->get();
        $tarianList    = Tarian::where('aktif', true)->get();

        // Rekap per anggota
        $rekap = $query->get()
            ->groupBy('user_id')
            ->map(function ($items) {
                return [
                    'user'   => $items->first()->user,
                    'hadir'  => $items->where('status', 'hadir')->count(),
                    'izin'   => $items->where('status', 'izin')->count(),
                    'alpa'   => $items->where('status', 'alpa')->count(),
                    'persen' => $items->count() > 0
                        ? round($items->where('status', 'hadir')->count() / $items->count() * 100)
                        : 0,
                ];
            })->values();

        return view('admin.kehadiran.laporan',
            compact('kehadiran', 'rekap', 'jadwalList', 'tarianList', 'bulan', 'jadwal_id', 'tarian_id'));
    }
}