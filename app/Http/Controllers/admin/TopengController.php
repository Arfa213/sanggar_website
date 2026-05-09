<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Topeng;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TopengController extends Controller
{
    public function index()
    {
        $topeng = Topeng::orderBy('urutan')->paginate(15);
        return view('admin.topeng.index', compact('topeng'));
    }

    public function create()
    {
        $topeng = new Topeng;
        $mode   = 'create';
        return view('admin.topeng.form', compact('topeng', 'mode'));
    }

    public function store(Request $request)
    {
        $data = $this->validateTopeng($request);
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('topeng', 'public');
        }
        $data['urutan'] = (Topeng::max('urutan') ?? 0) + 1;
        Topeng::create($data);
        return redirect()->route('admin.topeng.index')->with('success', 'Topeng berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $topeng = Topeng::findOrFail($id);
        $mode   = 'edit';
        return view('admin.topeng.form', compact('topeng', 'mode'));
    }

    public function update(Request $request, $id)
    {
        $topeng = Topeng::findOrFail($id);
        $data   = $this->validateTopeng($request);
        if ($request->hasFile('foto')) {
            if ($topeng->foto) Storage::disk('public')->delete($topeng->foto);
            $data['foto'] = $request->file('foto')->store('topeng', 'public');
        }
        $topeng->update($data);
        return redirect()->route('admin.topeng.index')->with('success', 'Topeng berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $topeng = Topeng::findOrFail($id);
        if ($topeng->foto) Storage::disk('public')->delete($topeng->foto);
        $topeng->delete();
        return redirect()->route('admin.topeng.index')->with('success', 'Topeng berhasil dihapus.');
    }

    private function validateTopeng(Request $request): array
    {
        return $request->validate([
            'nama'      => 'required|string|max:255',
            'warna'     => 'required|string|max:100',
            'karakter'  => 'required|string|max:255',
            'filosofi'  => 'nullable|string',
            'deskripsi' => 'nullable|string',
            'aktif'     => 'nullable|boolean',
            'foto'      => 'nullable|image|max:3072',
        ]);
    }
}
