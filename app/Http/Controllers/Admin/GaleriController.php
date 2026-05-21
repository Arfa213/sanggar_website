<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Galeri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GaleriController extends Controller
{
    public function index()
    {
        $galeri  = Galeri::orderBy('seksi')->orderBy('urutan')->paginate(24);
        $grouped = Galeri::orderBy('urutan')->get()->groupBy('seksi');
        return view('admin.media.index', compact('galeri', 'grouped'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'nullable|string|max:255',
            'seksi' => 'required|in:digital_archive,dokumentasi,hero,about',
            'tipe'  => 'required|in:foto,video',
            'file'  => 'required|file|mimes:jpg,jpeg,png,webp,mp4,mov|max:20480',
        ]);
        $path   = $request->file('file')->store('galeri/'.$request->seksi, 'public');
        $urutan = (Galeri::where('seksi', $request->seksi)->max('urutan') ?? 0) + 1;
        Galeri::create([
            'judul'  => $request->judul,
            'file'   => $path,
            'tipe'   => $request->tipe,
            'seksi'  => $request->seksi,
            'urutan' => $urutan,
            'aktif'  => true,
        ]);
        return back()->with('success', 'Media berhasil diunggah!');
    }

    public function destroy($id)
    {
        $galeri = Galeri::findOrFail($id);
        Storage::disk('public')->delete($galeri->file);
        $galeri->delete();
        return back()->with('success', 'Media berhasil dihapus.');
    }
}
