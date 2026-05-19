<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{SanggarProfile, Pelatih, Pengelola, JadwalLatihan};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /* ══════════════════════════════════════════════════════════
     *  PROFIL HALAMAN UTAMA
     * ══════════════════════════════════════════════════════════ */
    public function index()
    {
        $profil    = SanggarProfile::getInstance();
        $pelatih   = Pelatih::orderBy('urutan')->get();
        $pengelola = Pengelola::orderBy('urutan')->get();
        $jadwal    = JadwalLatihan::orderBy('urutan')->get();
        return view('admin.profile.index',
            compact('profil', 'pelatih', 'pengelola', 'jadwal'));
    }

    /* ── UPDATE PROFIL ────────────────────────────────────────── */
    public function updateProfil(Request $request)
    {
        $request->validate([
            'nama_sanggar'       => 'required|string|max:255',
            'tagline'            => 'nullable|string|max:500',
            'sejarah'            => 'required|string',
            'visi'               => 'required|string',
            'misi'               => 'required|array|min:1',
            'misi.*'             => 'required|string',
            'tahun_berdiri'      => 'nullable|string|max:10',
            'alamat'             => 'nullable|string',
            'no_hp'              => 'nullable|string|max:30',
            'email'              => 'nullable|email',
            'instagram'          => 'nullable|string',
            'jumlah_anggota'     => 'nullable|integer',
            'jumlah_penghargaan' => 'nullable|integer',
            'jumlah_event'       => 'nullable|integer',
            'foto_profil'        => 'nullable|image|max:2048',
            'foto_sejarah'       => 'nullable|image|max:2048',
        ]);

        $profil = SanggarProfile::getInstance();
        $data   = $request->except(['_token', '_method', 'foto_profil', 'foto_sejarah']);

        foreach (['foto_profil', 'foto_sejarah'] as $field) {
            if ($request->hasFile($field)) {
                if ($profil->$field) Storage::disk('public')->delete($profil->$field);
                $data[$field] = $request->file($field)->store($field, 'public');
            }
        }

        $profil->update($data);
        return back()->with('success', 'Profil sanggar berhasil diperbarui!');
    }

    /* ══════════════════════════════════════════════════════════
     *  PELATIH — CRUD LENGKAP
     * ══════════════════════════════════════════════════════════ */

    public function storePelatih(Request $request)
    {
        $data = $request->validate([
            'nama'         => 'required|string|max:255',
            'jabatan'      => 'required|string|max:255',
            'spesialisasi' => 'nullable|string|max:255',
            'pengalaman'   => 'nullable|string|max:100',
            'bio'          => 'nullable|string',
            'no_hp'        => 'nullable|string|max:30',
            'foto'         => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('pelatih', 'public');
        }
        $data['urutan'] = (Pelatih::max('urutan') ?? 0) + 1;
        $data['aktif']  = true;

        Pelatih::create($data);
        return back()->with('success', 'Pelatih berhasil ditambahkan!');
    }

    public function updatePelatih(Request $request, $id)
    {
        // Cari pelatih berdasarkan ID
        $pelatih = Pelatih::findOrFail($id);

        $data = $request->validate([
            'nama'         => 'required|string|max:255',
            'jabatan'      => 'required|string|max:255',
            'spesialisasi' => 'nullable|string|max:255',
            'pengalaman'   => 'nullable|string|max:100',
            'bio'          => 'nullable|string',
            'no_hp'        => 'nullable|string|max:30',
            'aktif'        => 'nullable',
            'foto'         => 'nullable|image|max:2048',
        ]);

        // Handle checkbox aktif
        $data['aktif'] = $request->has('aktif') ? 1 : 0;

        if ($request->hasFile('foto')) {
            if ($pelatih->foto) Storage::disk('public')->delete($pelatih->foto);
            $data['foto'] = $request->file('foto')->store('pelatih', 'public');
        } else {
            // Jangan overwrite foto lama jika tidak upload baru
            unset($data['foto']);
        }

        $pelatih->update($data);
        return back()->with('success', 'Data pelatih berhasil diperbarui!');
    }

    public function destroyPelatih($id)
    {
        $pelatih = Pelatih::findOrFail($id);
        if ($pelatih->foto) Storage::disk('public')->delete($pelatih->foto);
        $pelatih->delete();
        return back()->with('success', 'Pelatih berhasil dihapus.');
    }

    /* ══════════════════════════════════════════════════════════
     *  PENGELOLA — CRUD LENGKAP
     * ══════════════════════════════════════════════════════════ */

    public function storePengelola(Request $request)
    {
        $data = $request->validate([
            'nama'    => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'ikon'    => 'required|string|max:50',
            'foto'    => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('pengelola', 'public');
        }
        $data['urutan'] = (Pengelola::max('urutan') ?? 0) + 1;
        $data['aktif']  = true;

        Pengelola::create($data);
        return back()->with('success', 'Pengelola berhasil ditambahkan!');
    }

    public function updatePengelola(Request $request, $id)
    {
        $pengelola = Pengelola::findOrFail($id);

        $data = $request->validate([
            'nama'    => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'ikon'    => 'required|string|max:50',
            'aktif'   => 'nullable',
            'foto'    => 'nullable|image|max:2048',
        ]);

        $data['aktif'] = $request->has('aktif') ? 1 : 0;

        if ($request->hasFile('foto')) {
            if ($pengelola->foto) Storage::disk('public')->delete($pengelola->foto);
            $data['foto'] = $request->file('foto')->store('pengelola', 'public');
        } else {
            unset($data['foto']);
        }

        $pengelola->update($data);
        return back()->with('success', 'Data pengelola berhasil diperbarui!');
    }

    public function destroyPengelola($id)
    {
        $pengelola = Pengelola::findOrFail($id);
        if ($pengelola->foto) Storage::disk('public')->delete($pengelola->foto);
        $pengelola->delete();
        return back()->with('success', 'Pengelola berhasil dihapus.');
    }

    /* ══════════════════════════════════════════════════════════
     *  JADWAL LATIHAN
     * ══════════════════════════════════════════════════════════ */

    public function storeJadwal(Request $request)
    {
        $data = $request->validate([
            'hari'        => 'required|string',
            'jam_mulai'   => 'required|string',
            'jam_selesai' => 'required|string',
            'kelas'       => 'required|string|max:255',
            'tempat'      => 'required|string|max:255',
        ]);
        $data['urutan'] = (JadwalLatihan::max('urutan') ?? 0) + 1;
        $data['aktif']  = true;

        JadwalLatihan::create($data);
        return back()->with('success', 'Jadwal berhasil ditambahkan!');
    }

    public function updateJadwal(Request $request, $id)
    {
        $jadwal = JadwalLatihan::findOrFail($id);

        $data = $request->validate([
            'hari'        => 'required|string',
            'jam_mulai'   => 'required|string',
            'jam_selesai' => 'required|string',
            'kelas'       => 'required|string|max:255',
            'tempat'      => 'required|string|max:255',
        ]);
        $data['aktif'] = $request->has('aktif') ? 1 : 0;

        $jadwal->update($data);
        return back()->with('success', 'Jadwal berhasil diperbarui!');
    }

    public function destroyJadwal($id)
    {
        JadwalLatihan::findOrFail($id)->delete();
        return back()->with('success', 'Jadwal berhasil dihapus.');
    }
}