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
}
