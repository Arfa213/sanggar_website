<?php
namespace App\Http\Controllers;
use App\Models\Event;

class EventController extends Controller {
    public function index() {
        $mendatang = Event::where('status','akan_datang')->orderBy('tanggal')->get();
        return view('pages.event', compact('mendatang'));
    }

    public function ajukan(\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'nama_pengaju'    => 'required|string|max:255',
            'no_hp_pengaju'   => 'required|string|max:20',
            'nama'            => 'required|string|max:255',
            'tanggal'         => 'required|date',
            'kategori'        => 'required|string',
            'portofolio_link' => 'nullable|url',
            'catatan_pengaju' => 'nullable|string',
        ]);

        $validated['status'] = 'pending_approval';
        $validated['is_external'] = true;
        $validated['lokasi'] = 'Sanggar Mulya Bhakti'; // Default location

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
        
        if ($request->hasFile('bukti_transfer')) {
            $validated['bukti_transfer'] = $request->file('bukti_transfer')->store('bukti_transfer', 'public');
            $validated['status_pembayaran'] = 'menunggu_verifikasi';
        } else {
            $validated['status_pembayaran'] = $event->is_berbayar ? 'menunggu_verifikasi' : 'gratis';
        }

        \App\Models\PesertaEvent::create($validated);

        return back()->with('success', 'Pendaftaran berhasil dikirim! E-Tiket atau konfirmasi selanjutnya akan dikirim ke WhatsApp Anda oleh Admin.');
    }
}
