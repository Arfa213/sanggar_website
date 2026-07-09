<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\UjianPendaftaran;
use App\Models\RaporPagelaran;
use App\Models\User;
use Illuminate\Http\Request;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdminUjianController extends Controller
{
    public function index($eventId)
    {
        $event = Event::where('kategori', 'midhang_sore')->findOrFail($eventId);

        $pendaftar = UjianPendaftaran::with(['user', 'tarian'])
            ->where('event_id', $eventId)
            ->get();

        $stats = [
            'menunggu' => $pendaftar->where('status', 'menunggu')->count(),
            'diterima' => $pendaftar->where('status', 'diterima')->count(),
            'ditolak'  => $pendaftar->where('status', 'ditolak')->count(),
        ];

        return view('admin.ujian.index', compact('event', 'pendaftar', 'stats'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status'        => 'required|in:diterima,ditolak',
            'catatan_admin' => 'nullable|string|max:500',
        ]);

        $pendaftaran = UjianPendaftaran::with(['event', 'user', 'tarian'])->findOrFail($id);
        $pendaftaran->update([
            'status'        => $request->status,
            'catatan_admin' => $request->catatan_admin,
        ]);

        // Kirim notifikasi FCM ke user
        if ($pendaftaran->user->fcm_token) {
            $title = $request->status === 'diterima' ? 'Pendaftaran Ujian Diterima! ✅' : 'Pendaftaran Ujian Ditolak ❌';
            $body = $request->status === 'diterima'
                ? "Pendaftaran ujian Tari {$pendaftaran->tarian->nama} Anda telah disetujui. Silakan persiapkan diri Anda!"
                : "Mohon maaf, pendaftaran ujian Tari {$pendaftaran->tarian->nama} Anda ditolak. Catatan: " . ($request->catatan_admin ?? '-');
            
            $this->sendFcmNotification($pendaftaran->user->fcm_token, $title, $body, [
                'type' => 'ujian_status',
                'ujian_id' => (string)$pendaftaran->id
            ]);
        }

        return back()->with('success', 'Status pendaftaran ujian berhasil diperbarui!');
    }

    public function formNilai($eventId)
    {
        $event = Event::where('kategori', 'midhang_sore')->findOrFail($eventId);

        // Ambil pendaftaran yang berstatus 'diterima'
        $peserta = UjianPendaftaran::with(['user', 'tarian'])
            ->where('event_id', $eventId)
            ->where('status', 'diterima')
            ->get();

        foreach ($peserta as $p) {
            // Cek apakah sudah pernah dinilai di rapor_pagelaran
            $p->rapor = RaporPagelaran::where([
                'event_id'  => $eventId,
                'user_id'   => $p->user_id,
                'tarian_id' => $p->tarian_id,
            ])->first();
        }

        return view('admin.ujian.form_nilai', compact('event', 'peserta'));
    }

    public function simpanNilai(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'rapor' => 'required|array',
            'rapor.*.user_id' => 'required|exists:users,id',
            'rapor.*.tarian_id' => 'required|exists:tarian,id',
            'rapor.*.nilai_kehadiran' => 'required|numeric|min:0|max:100',
            'rapor.*.nilai_teknik' => 'required|numeric|min:0|max:100',
            'rapor.*.nilai_hafalan' => 'required|numeric|min:0|max:100',
            'rapor.*.nilai_ekspresi' => 'required|numeric|min:0|max:100',
            'rapor.*.nilai_penampilan' => 'required|numeric|min:0|max:100',
            'rapor.*.catatan' => 'nullable|string',
        ]);

        $eventId = $request->event_id;
        $event = Event::findOrFail($eventId);
        $pelatihId = auth()->id();
        $tokensToPush = [];

        foreach ($request->rapor as $data) {
            $rapor = RaporPagelaran::updateOrCreate(
                [
                    'event_id'  => $eventId,
                    'user_id'   => $data['user_id'],
                    'tarian_id' => $data['tarian_id']
                ],
                [
                    'pelatih_id'       => $pelatihId,
                    'nilai_kehadiran'  => $data['nilai_kehadiran'],
                    'nilai_teknik'     => $data['nilai_teknik'],
                    'nilai_hafalan'    => $data['nilai_hafalan'],
                    'nilai_ekspresi'   => $data['nilai_ekspresi'],
                    'nilai_penampilan' => $data['nilai_penampilan'],
                    'catatan'          => $data['catatan'] ?? null,
                ]
            );

            // Ambil token FCM user
            $user = User::find($data['user_id']);
            if ($user && $user->fcm_token && !$rapor->notif_terkirim) {
                $tokensToPush[] = [
                    'token' => $user->fcm_token,
                    'rapor_id' => $rapor->id,
                    'predikat' => $rapor->predikat,
                    'nilai' => $rapor->nilai_akhir,
                    'lulus' => $rapor->lulus
                ];
            }
        }

        // Kirim push notification
        if (!empty($tokensToPush)) {
            $this->sendBatchFcmNotifications($tokensToPush, $event->nama);
        }

        return redirect()->route('admin.ujian.index', $eventId)
            ->with('success', 'Nilai Ujian Midhang Sore berhasil disimpan dan notifikasi telah dikirim ke murid!');
    }

    private function sendFcmNotification($token, $title, $body, $data = [])
    {
        try {
            $keyFilePath = storage_path('app/firebase-service-account.json');
            if (!file_exists($keyFilePath)) return;

            $client = new GoogleClient();
            $client->setAuthConfig($keyFilePath);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
            $accessToken = $client->fetchAccessTokenWithAssertion()['access_token'];

            $config = json_decode(file_get_contents($keyFilePath), true);
            $projectId = $config['project_id'];

            $payload = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body
                    ],
                    'data' => array_merge(['click_action' => 'FLUTTER_NOTIFICATION_CLICK'], $data)
                ]
            ];

            Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", $payload);

        } catch (\Exception $e) {
            Log::error("FCM Exception Ujian: " . $e->getMessage());
        }
    }

    private function sendBatchFcmNotifications($tokensData, $eventName)
    {
        try {
            $keyFilePath = storage_path('app/firebase-service-account.json');
            if (!file_exists($keyFilePath)) return;

            $client = new GoogleClient();
            $client->setAuthConfig($keyFilePath);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
            $accessToken = $client->fetchAccessTokenWithAssertion()['access_token'];

            $config = json_decode(file_get_contents($keyFilePath), true);
            $projectId = $config['project_id'];

            foreach ($tokensData as $data) {
                $statusKelulusan = $data['lulus'] ? 'LULUS 🎉' : 'BELUM LULUS 📚';
                $payload = [
                    'message' => [
                        'token' => $data['token'],
                        'notification' => [
                            'title' => "Hasil Ujian Midhang Sore Keluar! 📊",
                            'body' => "Hasil ujian '{$eventName}' Anda: {$statusKelulusan} dengan Nilai: {$data['nilai']} ({$data['predikat']})."
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
                }
            }
        } catch (\Exception $e) {
            Log::error("FCM Batch Exception Ujian: " . $e->getMessage());
        }
    }
}
