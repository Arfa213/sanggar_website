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
        if ($request->filled('tipe'))   $query->where('tipe_anggota', $request->tipe);

        $anggota = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        return view('admin.anggota.index', compact('anggota'));
    }

    // ── Export PDF semua anggota ──────────────────────────────────
    public function downloadPdf(Request $request)
    {
        $tipe = $request->tipe ?? 'semua';
        $query = User::where('role', 'anggota')->orderBy('name');
        if ($tipe !== 'semua') $query->where('tipe_anggota', $tipe);
        $anggota = $query->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.anggota.pdf', compact('anggota', 'tipe'));
        $label = match($tipe) {
            'sementara' => 'Sementara',
            default     => 'Semua',
        };
        return $pdf->download("Data-Anggota-{$label}-SMB-" . now()->format('Y-m-d') . '.pdf');
    }

    // ── Export Excel ───────────────────────────────────
    public function downloadExcel(Request $request)
    {
        $tipe = $request->tipe ?? 'semua';
        $query = User::where('role', 'anggota')->orderBy('name');
        if ($tipe !== 'semua') $query->where('tipe_anggota', $tipe);
        $anggota = $query->get();

        $label = match($tipe) {
            'sementara' => 'Sementara',
            'tetap'     => 'Tetap',
            default     => 'Semua',
        };
        $filename = "Data-{$label}-SMB-" . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($anggota, $tipe) {
            $handle = fopen('php://output', 'w');
            // BOM untuk Excel agar bisa baca UTF-8
            fwrite($handle, "\xEF\xBB\xBF");

            // Header kolom
            fputcsv($handle, ['No', 'Nama', 'Email', 'No. HP', 'Alamat', 'Tipe', 'Status', 'Terdaftar', 'Berlaku Hingga', 'Catatan']);

            foreach ($anggota as $i => $a) {
                fputcsv($handle, [
                    $i + 1,
                    $a->name,
                    $a->email,
                    $a->no_hp ?? '-',
                    $a->alamat ?? '-',
                    $a->tipe_anggota_label,
                    ucfirst($a->status),
                    $a->created_at->format('d/m/Y'),
                    $a->tgl_kadaluarsa ? $a->tgl_kadaluarsa->format('d/m/Y') : '-',
                    $a->catatan_keanggotaan ?? '-',
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
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
            'name'                 => 'required|string|max:255',
            'email'                => 'required|email|unique:users,email',
            'alamat'               => 'nullable|string',
            'no_hp'                => 'nullable|string|max:30',
            'password'             => 'required|min:8|confirmed',
            'status'               => 'required|in:aktif,nonaktif',
            'tipe_anggota'         => 'required|in:tetap,sementara',
            'tgl_kadaluarsa'       => 'nullable|date',
            'catatan_keanggotaan'  => 'nullable|string|max:500',
            'foto'                 => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = [
            'name'                => $request->name,
            'email'               => $request->email,
            'alamat'              => $request->alamat,
            'no_hp'               => $request->no_hp,
            'password'            => Hash::make($request->password),
            'role'                => 'anggota',
            'status'              => $request->status,
            'tipe_anggota'        => $request->tipe_anggota,
            'tgl_kadaluarsa'      => $request->tgl_kadaluarsa,
            'catatan_keanggotaan' => $request->catatan_keanggotaan,
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
            'name'                => 'required|string|max:255',
            'email'               => 'required|email|unique:users,email,'.$anggota->id,
            'alamat'              => 'nullable|string',
            'no_hp'               => 'nullable|string|max:30',
            'status'              => 'required|in:aktif,nonaktif',
            'tipe_anggota'        => 'required|in:tetap,sementara',
            'tgl_kadaluarsa'      => 'nullable|date',
            'catatan_keanggotaan' => 'nullable|string|max:500',
            'password'            => 'nullable|min:8|confirmed',
            'foto'                => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only('name','email','alamat','no_hp','status','tipe_anggota','tgl_kadaluarsa','catatan_keanggotaan');
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