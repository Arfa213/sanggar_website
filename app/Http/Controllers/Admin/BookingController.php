<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranTari;
use App\Models\User;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $filterStatus = $request->get('status', 'semua');

        $query = PendaftaranTari::with('user', 'tarian')
            ->whereHas('user', function($q) {
                $q->where('tipe_anggota', 'pengunjung');
            });

        if ($filterStatus !== 'semua') {
            $query->where('status', $filterStatus);
        }

        $bookings = $query->orderBy('created_at', 'desc')->get();

        // Hitung jumlah per status
        $countPending = PendaftaranTari::whereHas('user', fn($q) => $q->where('tipe_anggota','pengunjung'))
            ->where('status','pending')->count();
        $countAktif = PendaftaranTari::whereHas('user', fn($q) => $q->where('tipe_anggota','pengunjung'))
            ->where('status','aktif')->count();
        $countDitolak = PendaftaranTari::whereHas('user', fn($q) => $q->where('tipe_anggota','pengunjung'))
            ->where('status','ditolak')->count();

        return view('admin.booking.index', compact(
            'bookings', 'filterStatus', 'countPending', 'countAktif', 'countDitolak'
        ));
    }

    public function confirm($id)
    {
        $booking = PendaftaranTari::with('user', 'tarian')->findOrFail($id);
        $booking->update(['status' => 'aktif']);

        // Generate WhatsApp link untuk notifikasi
        $waLink = $this->generateWaLink($booking, 'konfirmasi');

        return back()
            ->with('success', 'Booking berhasil dikonfirmasi.')
            ->with('wa_link', $waLink)
            ->with('wa_name', $booking->user->name);
    }

    public function reject($id)
    {
        $booking = PendaftaranTari::with('user', 'tarian')->findOrFail($id);
        $booking->update(['status' => 'ditolak']);

        // Generate WhatsApp link untuk notifikasi
        $waLink = $this->generateWaLink($booking, 'ditolak');

        return back()
            ->with('success', 'Booking telah ditolak.')
            ->with('wa_link', $waLink)
            ->with('wa_name', $booking->user->name);
    }

    public function destroy($id)
    {
        $booking = PendaftaranTari::findOrFail($id);
        $booking->delete();

        return back()->with('success', 'Data booking berhasil dihapus.');
    }

    public function getPendingCount()
    {
        $count = PendaftaranTari::where('status', 'pending')
            ->whereHas('user', function($q) {
                $q->where('tipe_anggota', 'pengunjung');
            })
            ->count();
        return response()->json(['count' => $count]);
    }

    /**
     * Generate URL WhatsApp dengan pesan yang sudah terformat
     */
    private function generateWaLink(PendaftaranTari $booking, string $aksi): ?string
    {
        $noHp = $booking->user->no_hp ?? null;
        if (!$noHp) return null;

        // Normalisasi nomor: 08xxx → 628xxx
        $noHp = preg_replace('/\D/', '', $noHp);
        if (str_starts_with($noHp, '0')) {
            $noHp = '62' . substr($noHp, 1);
        }

        $nama    = $booking->user->name;
        $tarian  = $booking->tarian->nama ?? 'Tarian';
        $tanggal = \Carbon\Carbon::parse($booking->tanggal_latihan)->translatedFormat('l, d F Y');
        $jam     = $booking->jam_latihan;

        if ($aksi === 'konfirmasi') {
            $pesan = "Halo {$nama}! 👋\n\n"
                   . "✅ *Booking sesi latihan Anda di Sanggar Mulya Bhakti telah DIKONFIRMASI!*\n\n"
                   . "📌 *Detail Sesi:*\n"
                   . "• Tarian  : {$tarian}\n"
                   . "• Tanggal : {$tanggal}\n"
                   . "• Jam     : {$jam} WIB\n\n"
                   . "Sampai jumpa di sanggar! 🎭\n"
                   . "— _Sanggar Mulya Bhakti_";
        } else {
            $pesan = "Halo {$nama}! 👋\n\n"
                   . "❌ *Booking sesi latihan Anda di Sanggar Mulya Bhakti tidak dapat dikonfirmasi.*\n\n"
                   . "📌 *Detail Sesi:*\n"
                   . "• Tarian  : {$tarian}\n"
                   . "• Tanggal : {$tanggal}\n"
                   . "• Jam     : {$jam} WIB\n\n"
                   . "Untuk informasi lebih lanjut, silakan hubungi sanggar kami.\n"
                   . "Terima kasih! 🙏\n"
                   . "— _Sanggar Mulya Bhakti_";
        }

        return 'https://wa.me/' . $noHp . '?text=' . urlencode($pesan);
    }
}


