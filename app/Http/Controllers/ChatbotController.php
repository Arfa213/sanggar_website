<?php
// app/Http/Controllers/ChatbotController.php
namespace App\Http\Controllers;

use App\Models\{ChatbotMessage, SanggarProfile, Tarian};
use App\Services\GeminiService;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    protected GeminiService $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    // ── Kirim pesan ke Gemini API ─────────────────────────────
    public function chat(Request $request)
    {
        $request->validate([
            'message'    => 'required|string|max:2000',
            'session_id' => 'required|string|max:100',
        ]);

        $sessionId = $request->session_id;
        $userId    = auth()->id();

        // Simpan pesan user
        ChatbotMessage::create([
            'session_id' => $sessionId,
            'user_id'    => $userId,
            'role'       => 'user',
            'content'    => $request->message,
        ]);

        // Riwayat percakapan (max 10 pesan terakhir, tidak termasuk pesan baru)
        $history = ChatbotMessage::where('session_id', $sessionId)
            ->orderBy('created_at')
            ->take(10)
            ->get(['role', 'content'])
            ->map(fn($m) => ['role' => $m->role, 'content' => $m->content])
            ->toArray();

        try {
            // Gunakan GeminiService baru yang sudah punya context sanggar
            $reply = $this->geminiService->generateResponse($request->message, $history);

            if (!$reply) {
                \Log::warning("Gemini API return empty reply for session: {$sessionId}");
                $reply = $this->getFallbackReply($request->message);
            }
        } catch (\Exception $e) {
            \Log::error("ChatbotController@chat Exception: " . $e->getMessage());
            $reply = $this->getFallbackReply($request->message);
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

    /**
     * Fallback reply ketika Gemini API tidak tersedia
     * Berdasarkan kata kunci dari pesan user
     */
    private function getFallbackReply(string $msg): string
    {
        $msgLower = strtolower($msg);

        // Cek tarian spesifik di database
        $tarianItem = Tarian::where('aktif', true)
            ->get()
            ->filter(function ($t) use ($msgLower) {
                return str_contains($msgLower, strtolower($t->nama));
            })->first();

        if ($tarianItem) {
            return "Tentu! **{$tarianItem->nama}** adalah tarian asal {$tarianItem->asal}. {$tarianItem->deskripsi} Tarian ini biasanya berfungsi sebagai {$tarianItem->fungsi}. Ingin tahu lebih banyak atau melihat videonya? Silakan cek di menu Arsip Digital kami! 🎭";
        }

        if (str_contains($msgLower, 'daftar') || str_contains($msgLower, 'gabung') || str_contains($msgLower, 'mendaftar')) {
            return "Untuk mendaftar sebagai anggota Sanggar Mulya Bhakti, klik tombol **Daftar Anggota** di pojok kanan atas halaman ini. Pendaftaran gratis! Setelah mendaftar, Anda bisa memilih kelas tari yang diminati. ✨";
        }
        if (str_contains($msgLower, 'jadwal') || str_contains($msgLower, 'latihan') || str_contains($msgLower, 'jam')) {
            return "Jadwal latihan kami: 📅\n- **Senin & Rabu**: 15.00–17.30 WIB\n- **Jumat**: 15.00–18.00 WIB\n- **Sabtu**: 08.00–11.00 WIB (anak-anak)\n- **Minggu**: 08.00–12.00 WIB (sesi gabungan)\n\nSemua di lokasi sanggar Indramayu.";
        }
        if (str_contains($msgLower, 'tari') || str_contains($msgLower, 'kelas')) {
            return "Kami mengajarkan tarian tradisional Indramayu seperti **Tari Topeng Kelana, Tari Sintren, Tari Ronggeng Bugis, Tari Baladewa, Tari Buyung**, dan masih banyak lagi! 🎭 Cek halaman Arsip Digital untuk informasi lengkap setiap tarian.";
        }
        if (str_contains($msgLower, 'biaya') || str_contains($msgLower, 'bayar') || str_contains($msgLower, 'harga') || str_contains($msgLower, 'gratis')) {
            return "Pendaftaran anggota di Sanggar Mulya Bhakti **GRATIS** 🎉 Untuk informasi biaya kelas privat atau event khusus, silakan hubungi kami langsung ya!";
        }
        if (str_contains($msgLower, 'kontak') || str_contains($msgLower, 'hubungi') || str_contains($msgLower, 'telepon') || str_contains($msgLower, 'wa') || str_contains($msgLower, 'whatsapp')) {
            try {
                $profil = SanggarProfile::getInstance();
                return "Hubungi kami di: 📞 **{$profil->no_hp}** | ✉️ **{$profil->email}** | Instagram: **{$profil->instagram}** 😊";
            } catch (\Exception $e) {
                return "Silakan hubungi kami melalui halaman Profil di website ini untuk informasi kontak lengkap! 😊";
            }
        }
        if (str_contains($msgLower, 'halo') || str_contains($msgLower, 'hai') || str_contains($msgLower, 'hello') || str_contains($msgLower, 'hi') || str_contains($msgLower, 'selamat')) {
            return "Halo! Selamat datang di Sanggar Mulya Bhakti 🎭 Saya asisten virtual kami. Ada yang bisa saya bantu?\n\nAnda bisa tanya tentang:\n- 📅 Jadwal latihan\n- 🎭 Tarian yang diajarkan\n- ✨ Cara mendaftar\n- 📞 Informasi kontak";
        }
        if (str_contains($msgLower, 'topeng') || str_contains($msgLower, 'indramayu') || str_contains($msgLower, 'budaya')) {
            return "Sanggar Mulya Bhakti adalah pusat pelestarian seni tari tradisional Indramayu, khususnya **Tari Topeng**. Kami berkomitmen melestarikan warisan budaya Jawa Barat sejak berdiri. 🎭 Cek koleksi topeng dan arsip tarian kami di menu Arsip Digital!";
        }

        return "Terima kasih atas pertanyaan Anda! 😊 Untuk informasi lebih lanjut tentang **Sanggar Mulya Bhakti**, silakan tanyakan seputar jadwal latihan, cara daftar, atau tarian yang kami ajarkan. Atau hubungi kami langsung melalui halaman Profil. 🎭";
    }

    public function clearHistory(Request $request)
    {
        ChatbotMessage::where('session_id', $request->session_id)->delete();
        return response()->json(['success' => true]);
    }

    public function recommendDance(Request $request)
    {
        $request->validate(['preference' => 'required|string|max:500']);

        $tarian   = Tarian::where('aktif', true)->get();
        $tariList = $tarian->map(fn($t) => "- {$t->nama}: {$t->deskripsi_singkat}")->implode("\n");
        $prompt   = "Kamu adalah konsultan tari Sanggar Mulya Bhakti. Calon murid memiliki karakter/preferensi: \"{$request->preference}\". Daftar tari yang tersedia:\n{$tariList}\n\nPilih SATU tari yang paling sesuai. Balas HANYA dengan JSON murni tanpa markdown: {\"tarian\":\"nama tari\",\"alasan\":\"alasan singkat dalam bahasa Indonesia\"}";

        try {
            $jawaban = $this->geminiService->generateResponse($prompt);
            // Bersihkan kemungkinan markdown code block dari Gemini
            $cleaned = trim(preg_replace('/```json|```/', '', $jawaban));
            $res     = json_decode($cleaned, true);

            if (!$res || !isset($res['tarian'])) {
                return response()->json([
                    'success' => false,
                    'error'   => 'Format jawaban tidak valid, silakan coba lagi.',
                ]);
            }

            return response()->json([
                'success' => true,
                'tarian'  => $res['tarian'],
                'alasan'  => $res['alasan'] ?? '',
            ]);
        } catch (\Exception $e) {
            \Log::error('RecommendDance error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error'   => 'Layanan rekomendasi sedang tidak tersedia.',
            ]);
        }
    }
}