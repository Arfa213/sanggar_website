<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PesertaEvent;
use Illuminate\Http\Request;

class PesertaEventController extends Controller
{
    public function index()
    {
        $peserta = PesertaEvent::with('event')->orderByDesc('created_at')->paginate(20);
        return view('admin.event.peserta', compact('peserta'));
    }

    public function updateStatus(Request $request, $id)
    {
        $peserta = PesertaEvent::findOrFail($id);
        
        $request->validate([
            'status_pembayaran' => 'required|in:gratis,menunggu_verifikasi,lunas,ditolak',
            'catatan_admin' => 'nullable|string'
        ]);

        if (!$peserta->order_id) {
            $peserta->order_id = 'EVT-' . time() . '-' . rand(100, 999);
        }

        $peserta->update([
            'status_pembayaran' => $request->status_pembayaran,
            'catatan_admin' => $request->catatan_admin,
            'order_id' => $peserta->order_id
        ]);

        // Kirim WA otomatis ke peserta dengan link PDF Tiket
        if ($peserta->status_pembayaran === 'lunas' || $peserta->status_pembayaran === 'gratis') {
            $tiketUrl = route('event.tiket', $peserta->order_id);
            $waText = urlencode("Halo Kak {$peserta->nama_peserta}, Pembayaran tiket Anda untuk event *{$peserta->event->nama}* telah LUNAS.\n\nSilakan unduh E-Tiket Anda melalui link berikut:\n{$tiketUrl}\n\nSampai jumpa di lokasi!");
            $waLink = "https://wa.me/" . preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $peserta->no_hp)) . "?text={$waText}";
            return redirect()->back()->with('wa_link', $waLink)->with('success', 'Status peserta berhasil diupdate. Membuka WhatsApp untuk mengirim E-Tiket...');
        }

        return redirect()->back()->with('success', 'Status peserta berhasil diupdate.');
    }

    public function destroy($id)
    {
        $peserta = PesertaEvent::findOrFail($id);
        $peserta->delete();
        return redirect()->back()->with('success', 'Data peserta berhasil dihapus.');
    }
}
