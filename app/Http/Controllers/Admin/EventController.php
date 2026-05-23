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
        $events = Event::where('status', '!=', 'pending_approval')->orderByDesc('tanggal')->paginate(15);
        $pendingEvents = Event::where('status', 'pending_approval')->orderByDesc('created_at')->get();
        return view('admin.event.index', compact('events', 'pendingEvents'));
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
        $data['unggulan'] = $request->has('unggulan');
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
        $data['unggulan'] = $request->has('unggulan');
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

    public function approve($id)
    {
        $event = Event::findOrFail($id);
        $event->update(['status' => 'akan_datang']);
        
        $waText = urlencode("Halo Kak {$event->nama_pengaju}, kami dari Sanggar Mulya Bhakti ingin mengabarkan bahwa pengajuan workshop/event Anda ({$event->nama}) telah kami setujui! Saat ini event tersebut sudah resmi tayang di website kami. Mari kita diskusikan teknis selanjutnya...");
        $waLink = "https://wa.me/" . preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $event->no_hp_pengaju)) . "?text={$waText}";

        return redirect()->route('admin.event.index')->with('wa_link', $waLink)->with('success', 'Event berhasil disetujui dan ditayangkan!');
    }

    private function validateEvent(Request $request): array
    {
        return $request->validate([
            'nama'            => 'required|string|max:255',
            'lokasi'          => 'required|string|max:255',
            'tanggal'         => 'required|date',
            'kategori'        => 'required|in:internasional,nasional,festival,pentas,kompetisi,workshop,kelas_khusus',
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
