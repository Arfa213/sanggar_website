<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\RaporPagelaran;
use App\Models\User;
use App\Models\PendaftaranTari;
use App\Models\Kehadiran;
use Illuminate\Http\Request;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RaporPagelaranController extends Controller
{
    public function index()
    {
        // Hanya ambil event berkategori pagelaran
        $pagelarans = Event::where('kategori', 'pagelaran')->orderByDesc('tanggal')->get();

        // Hitung statistik untuk masing-masing pagelaran
        foreach ($pagelarans as $pagelaran) {
            $pagelaran->total_dinilai = RaporPagelaran::where('event_id', $pagelaran->id)->count();
            
            // Total anggota tetap yang aktif
            $pagelaran->total_anggota = User::where('role', 'anggota')
                ->where('tipe_anggota', 'anggota_tetap')
                ->where('status', 'aktif')
                ->count();
        }

        return view('admin.rapor.index', compact('pagelarans'));
    }

    public function form($eventId)
    {
        $pagelaran = Event::findOrFail($eventId);
        
        // Ambil event pagelaran sebelumnya untuk menentukan periode absensi
        $previousPagelaran = Event::where('kategori', 'pagelaran')
            ->where('tanggal', '<', $pagelaran->tanggal)
            ->orderByDesc('tanggal')
            ->first();
            
        $startDate = $previousPagelaran ? $previousPagelaran->tanggal : null;
        $endDate = $pagelaran->tanggal;

        // Ambil semua anggota tetap yang aktif dan sudah terverifikasi
        $anggotaTetap = User::where('role', 'anggota')
            ->where('tipe_anggota', 'anggota_tetap')
            ->where('status', 'aktif')
            ->where(function($q) {
                $q->whereNotNull('email_verified_at')
                  ->orWhere('created_at', '<', '2026-05-21');
            })
            ->orderBy('name')
            ->get();

        foreach ($anggotaTetap as $anggota) {
            // Cek tarian utama yang sedang diikuti (ambil dari pendaftaran terbaru)
            $pendaftaran = PendaftaranTari::with('tarian')
                ->where('user_id', $anggota->id)
                ->where('status', 'aktif')
                ->latest()
                ->first();
                
            $anggota->tarian_id = $pendaftaran ? $pendaftaran->tarian_id : null;
            $anggota->tarian_nama = $pendaftaran && $pendaftaran->tarian ? $pendaftaran->tarian->nama : 'Belum memilih tarian';

            // Hitung % kehadiran periode ini
            $queryKehadiran = Kehadiran::where('user_id', $anggota->id)
                ->where('tanggal', '<=', $endDate);
                
            if ($startDate) {
                $queryKehadiran->where('tanggal', '>', $startDate);
            }
            
            $totalSesi = $queryKehadiran->count();
            $totalHadir = (clone $queryKehadiran)->where('status', 'hadir')->count();
            
            $anggota->persen_kehadiran = $totalSesi > 0 ? round(($totalHadir / $totalSesi) * 100, 2) : 0;

            // Ambil data rapor yang sudah ada (jika sedang mode edit)
            $existingRapor = RaporPagelaran::where('event_id', $eventId)
                ->where('user_id', $anggota->id)
                ->first();
                
            $anggota->rapor = $existingRapor;
        }

        return view('admin.rapor.form', compact('pagelaran', 'anggotaTetap', 'startDate', 'endDate'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'rapor' => 'required|array',
            'rapor.*.user_id' => 'required|exists:users,id',
            'rapor.*.tarian_id' => 'required|exists:tarian,id',
            'rapor.*.nilai_teknik' => 'required|numeric|min:0|max:100',
            'rapor.*.nilai_hafalan' => 'required|numeric|min:0|max:100',
            'rapor.*.nilai_ekspresi' => 'required|numeric|min:0|max:100',
            'rapor.*.nilai_penampilan' => 'required|numeric|min:0|max:100',
            'rapor.*.nilai_kehadiran' => 'required|numeric|min:0|max:100',
            'rapor.*.catatan' => 'nullable|string',
        ]);

        $eventId = $request->event_id;
        $pagelaran = Event::findOrFail($eventId);
        $pelatihId = auth()->id();
        $tokensToPush = [];
        $raporDataList = [];

        foreach ($request->rapor as $data) {
            $rapor = RaporPagelaran::updateOrCreate(
                [
                    'event_id' => $eventId,
                    'user_id' => $data['user_id'],
                    'tarian_id' => $data['tarian_id']
                ],
                [
                    'pelatih_id' => $pelatihId,
                    'nilai_teknik' => $data['nilai_teknik'],
                    'nilai_hafalan' => $data['nilai_hafalan'],
                    'nilai_ekspresi' => $data['nilai_ekspresi'],
                    'nilai_penampilan' => $data['nilai_penampilan'],
                    'nilai_kehadiran' => $data['nilai_kehadiran'],
                    'catatan' => $data['catatan'] ?? null,
                ]
            );

            // Ambil FCM token dari user
            $user = User::find($data['user_id']);
            if ($user && $user->fcm_token && !$rapor->notif_terkirim) {
                $tokensToPush[] = [
                    'token' => $user->fcm_token,
                    'rapor_id' => $rapor->id,
                    'predikat' => $rapor->predikat,
                    'nilai' => $rapor->nilai_akhir
                ];
            }
        }

        // Kirim notifikasi FCM
        if (!empty($tokensToPush)) {
            $this->sendFcmNotifications($tokensToPush, $pagelaran->nama);
        }

        return redirect()->route('admin.rapor.index')
            ->with('success', 'Nilai Rapor berhasil disimpan dan notifikasi telah dikirim ke murid!');
    }

    public function show($userId)
    {
        $murid = User::findOrFail($userId);
        
        $rapors = RaporPagelaran::with(['event', 'tarian', 'pelatih'])
            ->where('user_id', $userId)
            ->join('events', 'rapor_pagelaran.event_id', '=', 'events.id')
            ->orderBy('events.tanggal', 'asc')
            ->select('rapor_pagelaran.*')
            ->get();

        return view('admin.rapor.show', compact('murid', 'rapors'));
    }

    private function sendFcmNotifications($tokensData, $eventName)
    {
        try {
            $keyFilePath = storage_path('app/firebase-service-account.json');
            if (!file_exists($keyFilePath)) {
                Log::warning('Firebase service account file not found for Rapor notifications.');
                return;
            }

            $client = new GoogleClient();
            $client->setAuthConfig($keyFilePath);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
            
            $accessToken = $client->fetchAccessTokenWithAssertion()['access_token'];
            
            // Ambil project ID dari json config
            $config = json_decode(file_get_contents($keyFilePath), true);
            $projectId = $config['project_id'];

            foreach ($tokensData as $data) {
                $payload = [
                    'message' => [
                        'token' => $data['token'],
                        'notification' => [
                            'title' => 'Rapor Pagelaran Tersedia 📊',
                            'body' => "Rapor '{$eventName}' sudah keluar! Nilai Akhir kamu: {$data['nilai']} ({$data['predikat']})."
                        ],
                        'data' => [
                            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                            'type' => 'rapor',
                            'rapor_id' => (string)$data['rapor_id']
                        ]
                    ]
                ];

                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ])->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", $payload);

                if ($response->successful()) {
                    RaporPagelaran::where('id', $data['rapor_id'])->update(['notif_terkirim' => true]);
                } else {
                    Log::error("FCM Error Sending Rapor Notif: " . $response->body());
                }
            }
        } catch (\Exception $e) {
            Log::error("FCM Exception: " . $e->getMessage());
        }
    }
}
