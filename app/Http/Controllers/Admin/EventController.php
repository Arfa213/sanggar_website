<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::orderByDesc('tanggal')->paginate(15);
        return view('admin.event.index', compact('events'));
    }

    public function create()
    {
        $event = new Event;
        $mode  = 'create';
        return view('admin.event.form', compact('event', 'mode'));
    }

    public function store(Request $request)
    {
        $data = $this->validateEvent($request);
        $data = $this->handleFoto($request, $data);
        $data['penghargaan'] = $this->parsePenghargaan($request);
        Event::create($data);
        return redirect()->route('admin.event.index')->with('success', 'Event berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $event = Event::findOrFail($id);
        $mode  = 'edit';
        return view('admin.event.form', compact('event', 'mode'));
    }

    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        $data  = $this->validateEvent($request);
        $data  = $this->handleFoto($request, $data, $event);
        $data['penghargaan'] = $this->parsePenghargaan($request);
        $event->update($data);
        return redirect()->route('admin.event.index')->with('success', 'Event berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        if ($event->foto) Storage::disk('public')->delete($event->foto);
        $event->delete();
        return redirect()->route('admin.event.index')->with('success', 'Event berhasil dihapus.');
    }

    private function validateEvent(Request $request): array
    {
        return $request->validate([
            'nama'            => 'required|string|max:255',
            'lokasi'          => 'required|string|max:255',
            'tanggal'         => 'required|date',
            'kategori'        => 'required|in:internasional,nasional,festival,pentas,kompetisi',
            'level'           => 'required|in:Internasional,Nasional,Lokal',
            'hasil'           => 'nullable|string|max:255',
            'deskripsi'       => 'nullable|string',
            'jumlah_penonton' => 'nullable|integer',
            'unggulan'        => 'nullable|boolean',
            'status'          => 'required|in:akan_datang,selesai',
            'foto'            => 'nullable|image|max:3072',
        ]);
    }

    private function handleFoto(Request $request, array $data, ?Event $event = null): array
    {
        if ($request->hasFile('foto')) {
            if ($event?->foto) Storage::disk('public')->delete($event->foto);
            $data['foto'] = $request->file('foto')->store('events', 'public');
        }
        return $data;
    }

    private function parsePenghargaan(Request $request): array
    {
        $raw = $request->input('penghargaan', '');
        if (is_array($raw)) return array_filter($raw);
        return array_filter(array_map('trim', explode("\n", $raw)));
    }
}
