<?php
namespace App\Http\Controllers;
use App\Models\{SanggarProfile, Pelatih, Pengelola, JadwalLatihan, Event};

class ProfileController extends Controller {
    public function index() {
        $profil    = SanggarProfile::getInstance();
        $pelatih   = Pelatih::where('aktif', true)->orderBy('urutan')->get();
        $pengelola = Pengelola::where('aktif', true)->orderBy('urutan')->get();
        $jadwal     = JadwalLatihan::where('aktif', true)->orderBy('urutan')->get();
        $pastEvents = Event::where('status', '!=', 'pending_approval')
                           ->where(function($q) {
                               $q->where('status', 'selesai')
                                 ->orWhereDate('tanggal', '<', now());
                           })->orderByDesc('tanggal')->get();
        $byYear     = $pastEvents->groupBy(fn($e) => $e->tanggal->year);
        return view('pages.profile', compact('profil','pelatih','pengelola','jadwal','pastEvents','byYear'));
    }
}
