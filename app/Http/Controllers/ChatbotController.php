<?php
// app/Http/Controllers/ChatbotController.php
namespace App\Http\Controllers;

use App\Models\{ChatbotMessage, SanggarProfile, Tarian};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatbotController extends Controller
{
    // ── Kirim pesan ke Gemini API ─────────────────────────────
    public function chat(Request $request)
    {
        $request->validate([
            'message'    => 'required|string|max:2000',
            'session_id' => 'required|string|max:100',
        ]);

        $sessionId = $request->session_id;
        $userId    = auth()->id();
        $apiKey    = config('services.gemini.key'); // Set di .env: GEMINI_API_KEY=xxx

        // Simpan pesan user
        ChatbotMessage::create([
            'session_id' => $sessionId,
            'user_id'    => $userId,
            'role'       => 'user',
            'content'    => $request->message,
        ]);

        // Riwayat percakapan (max 10 pesan terakhir)
        $history = ChatbotMessage::where('session_id', $sessionId)
            ->orderBy('created_at')
            ->take(10)
            ->get();

        // Buat konteks sanggar
        $profil  = SanggarProfile::getInstance();
        $tarian  = Tarian::where('aktif', true)->pluck('nama')->implode(', ');
        $context = "Kamu adalah asisten virtual Sanggar Mulya Bhakti, sanggar seni tari tradisional dari Indramayu, Jawa Barat. " .
            "Informasi sanggar: {$profil->nama_sanggar}, berdiri sejak {$profil->tahun_berdiri}. " .
            "Tarian yang diajarkan: {$tarian}. " .
            "Kontak: {$profil->no_hp}, {$profil->email}. " .
            "Selalu jawab dengan sopan, dalam Bahasa Indonesia, dan fokus pada informasi sanggar. " .
            "Jika ditanya di luar konteks sanggar, arahkan kembali ke topik sanggar.";

        // Format history untuk Gemini
        $contents = [];

        // System context sebagai pesan pertama
        $contents[] = [
            'role'  => 'user',
            'parts' => [['text' => $context]],
        ];
        $contents[] = [
            'role'  => 'model',
            'parts' => [['text' => 'Baik, saya siap membantu menjawab pertanyaan seputar Sanggar Mulya Bhakti!']],
        ];

        // Tambahkan history percakapan
        foreach ($history as $msg) {
            $contents[] = [
                'role'  => $msg->role === 'user' ? 'user' : 'model',
                'parts' => [['text' => $msg->content]],
            ];
        }

        try {
            $response = Http::withoutVerifying()
                ->timeout(30)
                ->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}",
                    ['contents' => $contents]
                );

            if ($response->successful()) {
                $reply = $response->json('candidates.0.content.parts.0.text')
                    ?? 'Maaf, saya tidak dapat memproses pertanyaan Anda saat ini.';
            } else {
                // Fallback jika API error
                $reply = $this->getFallbackReply($request->message, $profil);
            }

        } catch (\Exception $e) {
            $reply = $this->getFallbackReply($request->message, $profil);
        }

        // Simpan balasan assistant
        ChatbotMessage::create([
            'session_id' => $sessionId,
            'user_id'    => $userId,
            'role'       => 'assistant',
            'content'    => $reply,
        ]);

        return response()->json([
            'success' => true,
            'reply'   => $reply,
        ]);
    }

    // ── Fallback tanpa API ────────────────────────────────────
    private function getFallbackReply(string $msg, $profil): string
    {
        $msg = strtolower($msg);

        if (str_contains($msg, 'daftar') || str_contains($msg, 'gabung')) {
            return "Untuk mendaftar sebagai anggota Sanggar Mulya Bhakti, klik tombol **Daftar Anggota** di pojok kanan atas halaman ini. Pendaftaran gratis! Setelah mendaftar, Anda bisa memilih kelas tari yang diminati.";
        }
        if (str_contains($msg, 'jadwal') || str_contains($msg, 'latihan')) {
            return "Jadwal latihan kami: Senin & Rabu (15.00–17.30), Jumat (15.00–18.00), Sabtu (08.00–11.00 untuk anak-anak), Minggu (08.00–12.00 sesi gabungan). Semua di Jl. Kebudayaan No. 17, Indramayu.";
        }
        if (str_contains($msg, 'tari') || str_contains($msg, 'kelas')) {
            return "Kami mengajarkan tarian tradisional Indramayu seperti Tari Topeng Kelana, Tari Sintren, Tari Ronggeng Bugis, Tari Baladewa, Tari Buyung, dan masih banyak lagi. Cek halaman Arsip Digital untuk informasi lengkap!";
        }
        if (str_contains($msg, 'kontak') || str_contains($msg, 'hubungi')) {
            return "Hubungi kami di: 📞 {$profil->no_hp} | ✉️ {$profil->email} | Instagram: {$profil->instagram}";
        }
        if (str_contains($msg, 'halo') || str_contains($msg, 'hai') || str_contains($msg, 'hello')) {
            return "Halo! Selamat datang di Sanggar Mulya Bhakti 🎭 Saya asisten virtual kami. Ada yang bisa saya bantu? Anda bisa tanya tentang jadwal latihan, cara daftar, atau tarian yang kami ajarkan!";
        }

        return "Terima kasih atas pertanyaan Anda! Untuk informasi lebih lanjut tentang {$profil->nama_sanggar}, silakan hubungi kami di {$profil->no_hp} atau email {$profil->email}. Kami siap membantu! 😊";
    }

    // ── Hapus riwayat chat (opsional) ─────────────────────────
    public function clearHistory(Request $request)
    {
        ChatbotMessage::where('session_id', $request->session_id)->delete();
        return response()->json(['success' => true]);
    }
}