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
        $apiKey    = config('services.gemini.key');

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
        $contents[] = [
            'role'  => 'user',
            'parts' => [['text' => $context]],
        ];
        $contents[] = [
            'role'  => 'model',
            'parts' => [['text' => 'Baik, saya siap membantu menjawab pertanyaan seputar Sanggar Mulya Bhakti!']],
        ];

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
                    "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}",
                    ['contents' => $contents]
                );

            if ($response->successful()) {
                $reply = $response->json('candidates.0.content.parts.0.text');
                
                if (!$reply) {
                    \Log::warning("Gemini API return empty candidates. Check safety settings or prompt. Response: " . $response->body());
                    $reply = $this->getFallbackReply($request->message, $profil);
                }
            } else {
                \Log::error("Gemini API Error: " . $response->status() . " - " . $response->body());
                $reply = $this->getFallbackReply($request->message, $profil);
            }
        } catch (\Exception $e) {
            \Log::error("Chatbot Exception: " . $e->getMessage());
            $reply = $this->getFallbackReply($request->message, $profil);
        }

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

    private function getFallbackReply(string $msg, $profil): string
    {
        $msgLower = strtolower($msg);

        // Cari di database tarian jika menanyakan tarian spesifik
        $tarianItem = Tarian::where('aktif', true)
            ->get()
            ->filter(function($t) use ($msgLower) {
                return str_contains($msgLower, strtolower($t->nama));
            })->first();

        if ($tarianItem) {
            return "Tentu! **{$tarianItem->nama}** adalah tarian asal {$tarianItem->asal}. {$tarianItem->deskripsi} Tarian ini biasanya berfungsi sebagai {$tarianItem->fungsi}. Ingin tahu lebih banyak atau melihat videonya? Silakan cek di menu Arsip Digital kami!";
        }

        if (str_contains($msgLower, 'daftar') || str_contains($msgLower, 'gabung')) {
            return "Untuk mendaftar sebagai anggota Sanggar Mulya Bhakti, klik tombol **Daftar Anggota** di pojok kanan atas halaman ini. Pendaftaran gratis! Setelah mendaftar, Anda bisa memilih kelas tari yang diminati.";
        }
        if (str_contains($msgLower, 'jadwal') || str_contains($msgLower, 'latihan')) {
            return "Jadwal latihan kami: Senin & Rabu (15.00–17.30), Jumat (15.00–18.00), Sabtu (08.00–11.00 untuk anak-anak), Minggu (08.00–12.00 sesi gabungan). Semua di Jl. Kebudayaan No. 17, Indramayu.";
        }
        if (str_contains($msgLower, 'tari') || str_contains($msgLower, 'kelas')) {
            return "Kami mengajarkan tarian tradisional Indramayu seperti Tari Topeng Kelana, Tari Sintren, Tari Ronggeng Bugis, Tari Baladewa, Tari Buyung, dan masih banyak lagi. Cek halaman Arsip Digital untuk informasi lengkap!";
        }
        if (str_contains($msgLower, 'kontak') || str_contains($msgLower, 'hubungi')) {
            return "Hubungi kami di: 📞 {$profil->no_hp} | ✉️ {$profil->email} | Instagram: {$profil->instagram}";
        }
        if (str_contains($msgLower, 'halo') || str_contains($msgLower, 'hai') || str_contains($msgLower, 'hello')) {
            return "Halo! Selamat datang di Sanggar Mulya Bhakti 🎭 Saya asisten virtual kami. Ada yang bisa saya bantu? Anda bisa tanya tentang jadwal latihan, cara daftar, atau tarian yang kami ajarkan!";
        }
        return "Terima kasih atas pertanyaan Anda! Untuk informasi lebih lanjut tentang {$profil->nama_sanggar}, silakan hubungi kami di {$profil->no_hp} atau email {$profil->email}. Kami siap membantu! 😊";
    }

    public function clearHistory(Request $request)
    {
        ChatbotMessage::where('session_id', $request->session_id)->delete();
        return response()->json(['success' => true]);
    }

    public function recommendDance(Request $request)
    {
        $request->validate(['preference' => 'required|string|max:500']);
        $apiKey = config('services.gemini.key');
        $tarian = Tarian::where('aktif', true)->get();
        $tariList = $tarian->map(fn($t) => "- {$t->nama}: {$t->deskripsi_singkat}")->implode("\n");
        $prompt = "Calon murid bingung pilih tari. Karakter: \"{$request->preference}\". Daftar tari:\n{$tariList}\nPilih SATU tari. Balas JSON murni: {\"tarian\":\"..\",\"alasan\":\"..\"}.";

        try {
            $resp = Http::withoutVerifying()->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
                'contents' => [['parts' => [['text' => $prompt]]]]
            ]);
            $res = json_decode(trim(preg_replace('/```json|```/', '', $resp->json('candidates.0.content.parts.0.text'))), true);
            return response()->json(['success' => true, 'tarian' => $res['tarian'], 'alasan' => $res['alasan']]);
        } catch (\Exception $e) { return response()->json(['success' => false, 'error' => $e->getMessage()]); }
    }
}