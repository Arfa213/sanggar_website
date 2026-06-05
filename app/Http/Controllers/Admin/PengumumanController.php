<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\Request;

class PengumumanController extends Controller
{
    public function index()
    {
        $broadcasts = Pengumuman::orderBy('created_at', 'desc')->get();
        return view('admin.pengumuman.index', compact('broadcasts'));
    }

    public function store(Request $request, \App\Services\FcmService $fcmService)
    {
        $request->validate([
            'judul' => 'required|string|max:150',
            'konten' => 'required|string',
            'tipe' => 'required|string|in:announcement,event,info',
        ]);

        $pengumuman = Pengumuman::create($request->only('judul', 'konten', 'tipe'));

        // Kirim Push Notification ke seluruh member (Topic: pengumuman_smb)
        $fcmService->sendToTopic(
            'pengumuman_smb',
            $request->judul,
            $request->konten,
            [
                'type' => 'announcement',
                'pengumuman_id' => (string)$pengumuman->id
            ]
        );

        return back()->with('success', 'Pengumuman berhasil disebarkan ke aplikasi mobile anggota!');
    }

    public function destroy($id)
    {
        $broadcast = Pengumuman::findOrFail($id);
        $broadcast->delete();

        return back()->with('success', 'Pengumuman berhasil dihapus.');
    }
}
