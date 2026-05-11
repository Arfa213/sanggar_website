<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tarian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TarianController extends Controller
{
    public function index()
    {
        $tarian = Tarian::orderBy('urutan')->paginate(15);
        return view('admin.tarian.index', compact('tarian'));
    }

    public function downloadPdf()
    {
        $tarian = Tarian::orderBy('nama')->get();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.tarian.pdf', compact('tarian'));
        return $pdf->download('Data-Tarian-SMB-' . now()->format('Y-m-d') . '.pdf');
    }

    public function create()
    {
        $tarian = new Tarian;
        $mode   = 'create';
        return view('admin.tarian.form', compact('tarian', 'mode'));
    }

    public function store(Request $request)
    {
        $data = $this->validateTarian($request);
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('tarian', 'public');
        }
        $data['urutan'] = (Tarian::max('urutan') ?? 0) + 1;
        Tarian::create($data);
        return redirect()->route('admin.tarian.index')->with('success', 'Tarian berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $tarian = Tarian::findOrFail($id);
        $mode   = 'edit';
        return view('admin.tarian.form', compact('tarian', 'mode'));
    }

    public function update(Request $request, $id)
    {
        $tarian = Tarian::findOrFail($id);
        $data   = $this->validateTarian($request);
        if ($request->hasFile('foto')) {
            if ($tarian->foto) Storage::disk('public')->delete($tarian->foto);
            $data['foto'] = $request->file('foto')->store('tarian', 'public');
        }
        $tarian->update($data);
        return redirect()->route('admin.tarian.index')->with('success', 'Tarian berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $tarian = Tarian::findOrFail($id);
        if ($tarian->foto) Storage::disk('public')->delete($tarian->foto);
        $tarian->delete();
        return redirect()->route('admin.tarian.index')->with('success', 'Tarian berhasil dihapus.');
    }

    private function validateTarian(Request $request): array
    {
        $validated = $request->validate([
            'nama'            => 'required|string|max:255',
            'asal'            => 'required|string|max:255',
            'kategori'        => 'required|in:sakral,hiburan,penyambutan,ritual,perang',
            'jenis_kegiatan'  => 'required|in:tari,gamelan,drama,srimpi',
            'deskripsi'       => 'required|string',
            'fungsi'          => 'nullable|string|max:255',
            'kostum'          => 'nullable|string|max:255',
            'durasi'          => 'nullable|string|max:100',
            'video_url'       => 'nullable|url|max:500',
            'unggulan'        => 'nullable|boolean',
            'aktif'           => 'nullable|boolean',
            'foto'            => 'nullable|image|max:3072',
        ]);
        $validated['unggulan'] = $request->boolean('unggulan');
        $validated['aktif']    = $request->boolean('aktif');
        return $validated;
    }
}
