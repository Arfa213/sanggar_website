<?php
namespace App\Http\Controllers;
use App\Models\Event;

class EventController extends Controller {
    public function index() {
        $midhang = Event::where('status', '!=', 'pending_approval')->where('status', '!=', 'selesai')->whereDate('tanggal', '>=', now())->where('kategori', 'midhang_sore')->orderBy('tanggal')->get();
        $studi = Event::where('status', '!=', 'pending_approval')->where('status', '!=', 'selesai')->whereDate('tanggal', '>=', now())->where('kategori', 'studi_budaya')->orderBy('tanggal')->get();
        $pagelaran = Event::where('status', '!=', 'pending_approval')->where('status', '!=', 'selesai')->whereDate('tanggal', '>=', now())->where('kategori', 'pagelaran')->orderBy('tanggal')->get();
        $tarianList = \App\Models\Tarian::where('aktif', true)->orderBy('nama')->get();
        return view('pages.event', compact('midhang', 'studi', 'pagelaran', 'tarianList'));
    }

    public function ajukan(\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'nama_pengaju'      => 'required|string|max:255',
            'foto_pengaju'      => 'required|image|max:3072',
            'no_hp_pengaju'     => 'required|string|max:20',
            'nama'              => 'required|string|max:255',
            'portofolio_link'   => 'nullable|url',
            'sinopsis_link'     => 'nullable|url',
            'catatan_pengaju'   => 'nullable|string',
            'bulan_pelaksanaan' => 'required|string|in:Juli,Desember',
        ]);

        if ($request->hasFile('foto_pengaju')) {
            $validated['foto_pengaju'] = $request->file('foto_pengaju')->store('pengaju', 'public');
        }

        $bulan = $request->input('bulan_pelaksanaan');
        $catatan = $request->input('catatan_pengaju');
        $validated['catatan_pengaju'] = "Bulan Pilihan: " . $bulan . ($catatan ? "\nCatatan: " . $catatan : "");

        unset($validated['bulan_pelaksanaan']);

        $validated['tanggal'] = now()->addMonth(); // Admin will edit this later
        $validated['status'] = 'pending_approval';
        $validated['is_external'] = true;
        $validated['lokasi'] = 'Sanggar Mulya Bhakti'; // Default location
        $validated['kategori'] = 'midhang_sore';

        Event::create($validated);

        return back()->with('success', 'Pengajuan Anda berhasil dikirim! Kami akan menghubungi Anda melalui WhatsApp setelah melakukan review.');
    }

    public function daftar(\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'event_id'      => 'required|exists:events,id',
            'nama_peserta'  => 'required|string|max:255',
            'no_hp'         => 'required|string|max:20',
            'asal_instansi' => 'nullable|string|max:255',
            'bukti_transfer'=> 'nullable|image|max:3072'
        ]);

        $event = Event::findOrFail($request->event_id);
        
        $validated['order_id'] = 'EVT-' . time() . '-' . rand(100, 999);
        
        if ($request->hasFile('bukti_transfer')) {
            $validated['bukti_transfer'] = $request->file('bukti_transfer')->store('bukti_transfer', 'public');
            $validated['status_pembayaran'] = 'menunggu_verifikasi';
        } else {
            $validated['status_pembayaran'] = $event->is_berbayar ? 'menunggu_verifikasi' : 'gratis';
        }

        \App\Models\PesertaEvent::create($validated);

        return back()->with('success', 'Pendaftaran berhasil dikirim! E-Tiket atau konfirmasi selanjutnya akan dikirim ke WhatsApp Anda oleh Admin setelah dicek.');
    }

    public function midtransWebhook(\Illuminate\Http\Request $request) {
        $serverKey = config('midtrans.server_key');
        $hashed = hash("sha512", $request->order_id.$request->status_code.$request->gross_amount.$serverKey);
        
        if ($hashed == $request->signature_key) {
            $peserta = \App\Models\PesertaEvent::where('order_id', $request->order_id)->first();
            
            if ($peserta) {
                if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                    $peserta->update(['status_pembayaran' => 'lunas']);
                    // Kirim WA Otomatis
                } elseif ($request->transaction_status == 'cancel' || $request->transaction_status == 'deny' || $request->transaction_status == 'expire') {
                    $peserta->update(['status_pembayaran' => 'ditolak']);
                }
            }
            return response()->json(['status' => 'success']);
        }
        
        return response()->json(['status' => 'error'], 400);
    }

    public function tiketPdf($order_id) {
        $peserta = \App\Models\PesertaEvent::with('event')->where('order_id', $order_id)->firstOrFail();
        
        if ($peserta->status_pembayaran !== 'lunas' && $peserta->status_pembayaran !== 'gratis') {
            return abort(403, 'Tiket belum lunas. Silakan selesaikan pembayaran terlebih dahulu.');
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pages.tiket_pdf', compact('peserta'));
        return $pdf->download('Tiket_Event_' . $peserta->order_id . '.pdf');
    }
}
