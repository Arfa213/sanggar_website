<?php

namespace App\Services;

use Exception;
use App\Models\SanggarProfile;
use App\Models\Tarian;

class GeminiService
{
    protected string $apiKey;
    protected string $model = 'gemini-2.0-flash';
    protected string $apiUrl;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key') ?: env('GEMINI_API_KEY');

        if (!$this->apiKey) {
            throw new Exception('Gemini API Key tidak ditemukan. Cek file .env Anda.');
        }

        $this->apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";
    }

    /**
     * Bangun system instruction yang kaya konteks dari database
     * Jika DB tidak tersedia, gunakan data statis default
     */
    private function buildSystemInstruction(): string
    {
        // Data default (fallback jika DB tidak tersedia)
        $namaSanggar  = 'Sanggar Mulya Bhakti';
        $tahunBerdiri = '2005';
        $alamat       = 'Indramayu, Jawa Barat';
        $noHp         = '-';
        $email        = '-';
        $instagram    = '-';
        $daftarTarian = "- Tari Topeng Kelana\n- Tari Sintren\n- Tari Ronggeng Bugis\n- Tari Baladewa\n- Tari Buyung";

        // Coba ambil dari database (boleh gagal)
        try {
            $profil = SanggarProfile::getInstance();
            $namaSanggar  = $profil->nama_sanggar  ?: $namaSanggar;
            $tahunBerdiri = $profil->tahun_berdiri ?: $tahunBerdiri;
            $alamat       = $profil->alamat        ?: $alamat;
            $noHp         = $profil->no_hp         ?: $noHp;
            $email        = $profil->email         ?: $email;
            $instagram    = $profil->instagram     ?: $instagram;
        } catch (\Throwable $e) {
            // DB tidak tersedia — gunakan data default di atas
        }

        // Coba ambil daftar tarian dari database
        try {
            $tarian = Tarian::where('aktif', true)->get();
            if ($tarian->isNotEmpty()) {
                $daftarTarian = $tarian->map(fn($t) => "- {$t->nama}" . ($t->deskripsi_singkat ? ": {$t->deskripsi_singkat}" : ''))->implode("\n");
            }
        } catch (\Throwable $e) {
            // DB tidak tersedia — gunakan data default di atas
        }

        return <<<INSTRUCTION
Kamu adalah "Asisten Sanggar", asisten virtual resmi milik {$namaSanggar}.

IDENTITAS KAMU:
- Nama: Asisten Sanggar
- Milik: {$namaSanggar}
- Fokus: Sanggar seni tari tradisional dari Indramayu, Jawa Barat

INFORMASI SANGGAR:
- Nama: {$namaSanggar}
- Berdiri: {$tahunBerdiri}
- Lokasi: {$alamat}
- Telepon/WhatsApp: {$noHp}
- Email: {$email}
- Instagram: {$instagram}

TARIAN YANG DIAJARKAN:
{$daftarTarian}

JADWAL LATIHAN:
- Senin & Rabu: 15.00 – 17.30 WIB
- Jumat: 15.00 – 18.00 WIB
- Sabtu: 08.00 – 11.00 WIB (khusus anak-anak)
- Minggu: 08.00 – 12.00 WIB (sesi gabungan)

CARA DAFTAR:
- Pendaftaran gratis melalui website atau langsung ke sanggar
- Calon anggota bisa memilih kelas tari setelah mendaftar

ATURAN MENJAWAB:
1. Gunakan Bahasa Indonesia yang sopan, ramah, dan mudah dipahami.
2. HANYA jawab pertanyaan yang berkaitan dengan {$namaSanggar}, tari tradisional, seni budaya Indramayu, atau kegiatan sanggar.
3. Jika ditanya di luar topik sanggar (misalnya politik, teknologi umum, dll), tolak dengan sopan dan arahkan ke topik sanggar.
4. Jangan pernah memberikan informasi kontak selain yang tertera di atas.
5. Jangan berbohong atau mengarang informasi. Jika tidak tahu, arahkan pengguna untuk menghubungi sanggar langsung.
6. Gunakan emoji sesekali agar terasa ramah 🎭
7. Jawab dengan singkat dan jelas, tidak lebih dari 200 kata kecuali diminta detail.
INSTRUCTION;
    }

    /**
     * Kirim pesan ke Gemini API dengan context sanggar penuh
     */
    public function generateResponse(string $message, array $history = []): string
    {
        $systemInstruction = $this->buildSystemInstruction();

        // Bangun contents: tambahkan history percakapan jika ada
        $contents = [];

        // Inject context sebagai percakapan pertama
        $contents[] = [
            'role'  => 'user',
            'parts' => [['text' => $systemInstruction]],
        ];
        $contents[] = [
            'role'  => 'model',
            'parts' => [['text' => 'Baik, saya siap membantu sebagai Asisten Sanggar Mulya Bhakti! Silakan tanyakan apa pun seputar sanggar kami. 🎭']],
        ];

        // Tambahkan history percakapan
        foreach ($history as $hist) {
            $contents[] = [
                'role'  => $hist['role'] === 'user' ? 'user' : 'model',
                'parts' => [['text' => $hist['content']]],
            ];
        }

        // Pesan user saat ini
        $contents[] = [
            'role'  => 'user',
            'parts' => [['text' => $message]],
        ];

        $data = [
            'contents'         => $contents,
            'generationConfig' => [
                'temperature'     => 0.3, // Lebih rendah = jawaban lebih konsisten & tidak ngaco
                'topP'            => 0.85,
                'topK'            => 40,
                'maxOutputTokens' => 512,
            ],
            'safetySettings' => [
                ['category' => 'HARM_CATEGORY_HARASSMENT',        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                ['category' => 'HARM_CATEGORY_HATE_SPEECH',       'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
            ],
        ];

        $ch = curl_init($this->apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT        => 30,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!$response) {
            throw new Exception('Tidak dapat terhubung ke Gemini API. Periksa koneksi internet.');
        }

        $result = json_decode($response, true);

        if (isset($result['error'])) {
            throw new Exception("Gemini Error: " . $result['error']['message']);
        }

        $reply = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if (!$reply) {
            // Cek apakah diblokir oleh safety filter
            $finishReason = $result['candidates'][0]['finishReason'] ?? '';
            if ($finishReason === 'SAFETY') {
                return "Maaf, saya tidak dapat menjawab pertanyaan tersebut. Silakan tanyakan seputar Sanggar Mulya Bhakti! 😊";
            }
            return "Maaf, saya sedang mengalami gangguan teknis. Silakan coba lagi atau hubungi kami langsung.";
        }

        return $reply;
    }
}
