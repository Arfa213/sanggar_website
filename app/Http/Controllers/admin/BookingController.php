<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranTari;
use App\Models\User;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        // Ambil pendaftaran dari tipe 'pengunjung' (anggota sementara)
        $bookings = PendaftaranTari::with('user', 'tarian')
            ->whereHas('user', function($q) {
                $q->where('tipe_anggota', 'pengunjung');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.booking.index', compact('bookings'));
    }

    public function confirm($id)
    {
        $booking = PendaftaranTari::findOrFail($id);
        $booking->update(['status' => 'aktif']);

        return back()->with('success', 'Booking berhasil dikonfirmasi.');
    }

    public function reject($id)
    {
        $booking = PendaftaranTari::findOrFail($id);
        $booking->update(['status' => 'nonaktif']);

        return back()->with('success', 'Booking telah ditolak.');
    }

    public function destroy($id)
    {
        $booking = PendaftaranTari::findOrFail($id);
        $booking->delete();

        return back()->with('success', 'Data booking berhasil dihapus.');
    }
}
