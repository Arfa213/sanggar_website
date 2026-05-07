<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AnggotaController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'anggota');
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(fn($w) => $w->where('name','like',"%$q%")->orWhere('email','like',"%$q%"));
        }
        if ($request->filled('status')) $query->where('status', $request->status);
        $anggota = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        return view('admin.anggota.index', compact('anggota'));
    }

    public function downloadPdf()
    {
        $anggota = User::where('role', 'anggota')->orderBy('name')->get();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.anggota.pdf', compact('anggota'));
        return $pdf->download('Data-Anggota-SMB-' . now()->format('Y-m-d') . '.pdf');
    }

    public function create()
    {
        $anggota = new User;
        $mode    = 'create';
        return view('admin.anggota.form', compact('anggota', 'mode'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'alamat'   => 'nullable|string',
            'no_hp'    => 'nullable|string|max:30',
            'password' => 'required|min:8|confirmed',
            'status'   => 'required|in:aktif,nonaktif',
            'foto'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
        $data = [
            'name'     => $request->name,
            'email'    => $request->email,
            'alamat'   => $request->alamat,
            'no_hp'    => $request->no_hp,
            'password' => Hash::make($request->password),
            'role'     => 'anggota',
            'status'   => $request->status,
        ];

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('profil_anggota', 'public');
        }

        User::create($data);
        return redirect()->route('admin.anggota.index')->with('success', 'Anggota berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $anggota = User::findOrFail($id);
        abort_if($anggota->role === 'admin', 403);
        $mode = 'edit';
        return view('admin.anggota.form', compact('anggota', 'mode'));
    }

    public function update(Request $request, $id)
    {
        $anggota = User::findOrFail($id);
        abort_if($anggota->role === 'admin', 403);
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,'.$anggota->id,
            'alamat'   => 'nullable|string',
            'no_hp'    => 'nullable|string|max:30',
            'status'   => 'required|in:aktif,nonaktif',
            'password' => 'nullable|min:8|confirmed',
            'foto'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
        $data = $request->only('name','email','alamat','no_hp','status');
        if ($request->filled('password')) $data['password'] = Hash::make($request->password);

        if ($request->hasFile('foto')) {
            if ($anggota->foto && Storage::disk('public')->exists($anggota->foto)) {
                Storage::disk('public')->delete($anggota->foto);
            }
            $data['foto'] = $request->file('foto')->store('profil_anggota', 'public');
        }

        $anggota->update($data);
        return redirect()->route('admin.anggota.index')->with('success', 'Data anggota berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $anggota = User::findOrFail($id);
        abort_if($anggota->role === 'admin', 403);
        $anggota->delete();
        return redirect()->route('admin.anggota.index')->with('success', 'Anggota berhasil dihapus.');
    }

    public function toggleStatus($id)
    {
        $anggota = User::findOrFail($id);
        abort_if($anggota->role === 'admin', 403);
        $anggota->update(['status' => $anggota->status === 'aktif' ? 'nonaktif' : 'aktif']);
        return back()->with('success', 'Status anggota berhasil diperbarui.');
    }
}