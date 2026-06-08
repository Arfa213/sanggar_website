<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\GeminiService;
use App\Models\Chat;
use App\Models\ChatbotMessage;
use Exception;

class GeminiController extends Controller
{
    protected GeminiService $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Endpoint chat AI untuk Mobile App
     * POST /api/v1/ai/chat
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message'    => 'required|string|max:2000',
            'session_id' => 'nullable|string|max:100',
        ]);

        $userMessage = $request->input('message');
        $sessionId   = $request->input('session_id');
        $userId      = auth('sanctum')->id();

        if (!$userMessage) {
            return response()->json([
                'success' => false,
                'reply'   => 'Pesan tidak boleh kosong.',
            ], 400);
        }

        // Ambil history percakapan jika ada session_id
        $history = [];
        if ($sessionId) {
            $msgs = ChatbotMessage::where('session_id', $sessionId)
                ->orderByDesc('created_at')
                ->take(4)
                ->get(['role', 'content'])
                ->reverse();

            $history = $msgs->map(fn($m) => [
                'role'    => $m->role,
                'content' => $m->content,
            ])->toArray();
        }

        try {
            // Simpan pesan user
            if ($sessionId) {
                ChatbotMessage::create([
                    'session_id' => $sessionId,
                    'user_id'    => $userId,
                    'role'       => 'user',
                    'content'    => $userMessage,
                ]);
            }

            // Dapatkan jawaban dari Gemini dengan context & history
            $jawaban = $this->geminiService->generateResponse($userMessage, $history);

            // Simpan log ke tabel chats
            try {
                Chat::create([
                    'pesan_user' => $userMessage,
                    'jawaban_ai' => $jawaban,
                    'model_used' => 'gemini-2.0-flash',
                ]);
            } catch (\Exception $e) {
                // Log saja, jangan hentikan response
                \Log::warning('Gagal menyimpan log chat: ' . $e->getMessage());
            }

            // Simpan jawaban AI ke history
            if ($sessionId) {
                ChatbotMessage::create([
                    'session_id' => $sessionId,
                    'user_id'    => $userId,
                    'role'       => 'assistant',
                    'content'    => $jawaban,
                ]);
            }

            return response()->json([
                'success' => true,
                'reply'   => $jawaban,
            ]);

        } catch (Exception $e) {
            \Log::error('GeminiController@chat error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'reply'   => 'Maaf, asisten sedang tidak tersedia. Silakan coba beberapa saat lagi atau hubungi kami langsung.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Rekomendasikan tarian berdasarkan preferensi user
     * POST /api/v1/ai/recommend
     */
    public function recommendDance(Request $request)
    {
        $request->validate(['preference' => 'required|string|max:500']);

        $tarian   = \App\Models\Tarian::where('aktif', true)->get();
        $tariList = $tarian->map(fn($t) => "- {$t->nama}: {$t->deskripsi_singkat}")->implode("\n");
        $prompt   = "Kamu adalah konsultan tari Sanggar Mulya Bhakti. Calon murid memiliki karakter/preferensi: \"{$request->preference}\". Daftar tari yang tersedia:\n{$tariList}\n\nPilih SATU tari yang paling sesuai. Balas HANYA dengan JSON murni tanpa markdown: {\"tarian\":\"nama tari\",\"alasan\":\"alasan singkat dalam bahasa Indonesia\"}";

        try {
            $jawaban = $this->geminiService->generateResponse($prompt);
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
        } catch (Exception $e) {
            \Log::error('GeminiController@recommendDance error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error'   => 'Layanan rekomendasi sedang tidak tersedia.',
            ]);
        }
    }
}