<?php
// app/Http/Controllers/Admin/KehadiranController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Kehadiran, JadwalLatihan, Tarian, PendaftaranTari, User, SesiKehadiran, KelasBarcode};
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
        
        $statsHariIni['tamu'] = \App\Models\Pengunjung::whereDate('tanggal', $today)->count();

        // Pastikan ada SATU QR Universal
        $universalQR = KelasBarcode::firstOrCreate(
            ['barcode_token' => 'SMB-UNIVERSAL-QR'], 
            [
                'tarian_id' => null,
                'aktif' => true,
                'dibuat_oleh' => 'Sistem (Universal)'
            ]
        );

        // QR Khusus Tamu (Public URL)
        $guestQR = route('tamu.index');

        // Ambil QR Universal untuk ditampilkan
        $permanentQR = collect([$universalQR]);

        // Sesi yang sudah diinput manual hari ini
        $sesiHariIni = Kehadiran::with(['jadwal', 'tarian'])
            ->whereDate('tanggal', $today)
            ->select('jadwal_id', 'tarian_id')
            ->distinct()
            ->get();

        return view('admin.kehadiran.index', compact(
            'jadwal', 'tarian', 'statsHariIni', 'today', 'sesiHariIni', 'permanentQR', 'guestQR'
        ));
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

    // ── Laporan rekap kehadiran ───────────────────────────────────
    public function laporan(Request $request)
    {
        $bulan = $request->bulan ?? now()->format('Y-m');

        // Rekap per anggota (tetap tampilkan semua agar admin tahu persentase bulanan)
        $rekapQuery = Kehadiran::with(['user'])
            ->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$bulan]);
        
        $rekap = $rekapQuery->get()
            ->groupBy('user_id')
            ->map(function ($items) {
                $total = $items->count();
                return [
                    'user'   => $items->first()->user,
                    'hadir'  => $items->where('status', 'hadir')->count(),
                    'izin'   => $items->where('status', 'izin')->count(),
                    'alpa'   => $items->where('status', 'alpa')->count(),
                    'total'  => $total,
                    'persen' => $total > 0 ? round($items->where('status', 'hadir')->count() / $total * 100) : 0,
                ];
            })->values();

        // Ambil HANYA 3 TERBARU untuk detail di dashboard ringkasan
        $kehadiranLatest = Kehadiran::with(['user', 'jadwal', 'tarian'])
            ->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$bulan])
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        $pengunjungLatest = \App\Models\Pengunjung::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$bulan])
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam', 'desc')
            ->take(3)
            ->get();

        $jadwalList = JadwalLatihan::where('aktif', true)->get();
        $tarianList = Tarian::where('aktif', true)->get();

        return view('admin.kehadiran.laporan', compact(
            'kehadiranLatest', 'pengunjungLatest', 'rekap', 'jadwalList', 'tarianList', 'bulan'
        ));
    }

    /**
     * Halaman Detail Laporan Anggota (Pagination + Search)
     */
    public function laporanAnggota(Request $request)
    {
        $bulan = $request->bulan ?? now()->format('Y-m');
        $query = Kehadiran::with(['user', 'jadwal', 'tarian'])
            ->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$bulan]);

        if ($request->jadwal_id) $query->where('jadwal_id', $request->jadwal_id);
        if ($request->tarian_id) $query->where('tarian_id', $request->tarian_id);
        if ($request->tipe) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('tipe_anggota', $request->tipe);
            });
        }

        $kehadiran = $query->orderBy('tanggal', 'desc')->paginate(20);
        $jadwalList = JadwalLatihan::where('aktif', true)->get();
        $tarianList = Tarian::where('aktif', true)->get();

        return view('admin.kehadiran.laporan_detail_anggota', compact('kehadiran', 'jadwalList', 'tarianList', 'bulan'));
    }

    /**
     * Halaman Detail Laporan Pengunjung
     */
    public function laporanPengunjung(Request $request)
    {
        $bulan = $request->bulan ?? now()->format('Y-m');
        $pengunjung = \App\Models\Pengunjung::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$bulan])
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam', 'desc')
            ->paginate(20);

        return view('admin.kehadiran.laporan_detail_pengunjung', compact('pengunjung', 'bulan'));
    }

    /**
     * Export PDF (Unified for simplicity or specific type)
     */
    public function exportPdf(Request $request)
    {
        $type  = $request->type; // 'anggota' or 'pengunjung'
        $bulan = $request->bulan ?? now()->format('Y-m');
        
        if ($type === 'pengunjung') {
            $data = \App\Models\Pengunjung::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$bulan])
                ->orderBy('tanggal', 'desc')->get();
            $title = "Laporan Pengunjung - " . $bulan;
            $view  = 'admin.kehadiran.pdf_pengunjung';
        } else {
            $data = Kehadiran::with(['user', 'jadwal', 'tarian'])
                ->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$bulan])
                ->orderBy('tanggal', 'desc')->get();
            $title = "Laporan Kehadiran Anggota - " . $bulan;
            $view  = 'admin.kehadiran.pdf_anggota';
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($view, compact('data', 'title', 'bulan'));
        return $pdf->download($title . ".pdf");
    }

    public function showPermanentQR($id)
    {
        $qr = KelasBarcode::with(['jadwal', 'tarian'])->findOrFail($id);
        $scanUrl = $qr->barcode_token; 
        
        return view('admin.kehadiran.qr_view', compact('qr', 'scanUrl'));
    }

    public function deletePermanentQR($id)
    {
        KelasBarcode::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'QR Code Permanen berhasil dihapus.');
    }
}