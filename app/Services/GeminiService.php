<?php

namespace App\Services;

use Exception;
use Carbon\Carbon;
use App\Models\Event;
use App\Models\Topeng;
use App\Models\Pelatih;
use App\Models\Tarian;
use App\Models\Pengumuman;
use App\Models\Pengelola;
use App\Models\JadwalLatihan;
use App\Models\SanggarProfile;

class GeminiService
{
    protected string $apiKey;
    protected string $model;
    protected string $apiUrl;
    protected bool $isLiteLLM = false;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key') ?: env('GEMINI_API_KEY');
        $this->model  = config('services.gemini.model', 'gemini-2.0-flash');
        $baseUrl      = config('services.gemini.base_url', '');

        if (!$this->apiKey) {
            throw new Exception('Gemini API Key tidak ditemukan. Cek file .env Anda.');
        }

        // Deteksi otomatis: jika ada GEMINI_BASE_URL → pakai LiteLLM (OpenAI-compatible proxy)
        //                   jika tidak ada           → pakai Google Gemini API langsung
        if (!empty($baseUrl)) {
            $this->isLiteLLM = true;
            $this->apiUrl    = rtrim($baseUrl, '/') . '/chat/completions';
        } else {
            $this->isLiteLLM = false;
            $this->apiUrl    = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";
        }
    }

    // =========================================================================
    // SECTION 1: PENGAMBIL DATA DARI DATABASE
    // Setiap fungsi di sini mengambil satu jenis data dari DB,
    // dengan fallback aman jika DB error atau data kosong.
    // =========================================================================

    private function getProfilSanggar(): array
    {
        try {
            $profil = SanggarProfile::getInstance();
            return [
                'nama'               => $profil->nama_sanggar  ?? 'Sanggar Mulya Bhakti',
                'tagline'            => $profil->tagline        ?? 'Melestarikan Budaya Melalui Seni',
                'sejarah'            => $profil->sejarah        ?? '-',
                'visi'               => $profil->visi           ?? '-',
                'misi'               => is_array($profil->misi) ? $profil->misi : [],
                'tahun_berdiri'      => $profil->tahun_berdiri  ?? '2005',
                'lokasi_alamat'      => $profil->alamat         ?? 'Indramayu, Jawa Barat',
                'jumlah_anggota'     => $profil->jumlah_anggota     ?? 0,
                'jumlah_penghargaan' => $profil->jumlah_penghargaan ?? 0,
                'jumlah_event'       => $profil->jumlah_event       ?? 0,
                'kontak' => [
                    'telepon_whatsapp' => $profil->no_hp     ?? '-',
                    'email'            => $profil->email     ?? '-',
                    'instagram'        => $profil->instagram ?? '-',
                    'facebook'         => $profil->facebook  ?? '-',
                    'youtube'          => $profil->youtube   ?? '-',
                ],
            ];
        } catch (\Throwable $e) {
            return [
                'nama'           => 'Sanggar Mulya Bhakti',
                'tahun_berdiri'  => '2005',
                'lokasi_alamat'  => 'Indramayu, Jawa Barat',
                'kontak'         => ['telepon_whatsapp' => '-', 'email' => '-'],
            ];
        }
    }

    private function getTarianList(): array
    {
        try {
            $tarianList = Tarian::where('aktif', true)->orderBy('urutan')->get();
            if ($tarianList->isEmpty()) {
                return [['nama' => 'Tari Topeng Kelana'], ['nama' => 'Tari Sintren'], ['nama' => 'Tari Ronggeng Bugis']];
            }
            return $tarianList->map(fn($t) => [
                'nama'            => $t->nama,
                'asal'            => $t->asal            ?? '-',
                'kategori'        => $t->kategori        ?? '-',
                'jenis_kegiatan'  => $t->jenis_kegiatan  ?? '-',
                'deskripsi'       => $t->deskripsi       ?? '-',
                'fungsi'          => $t->fungsi          ?? '-',
                'kostum'          => $t->kostum          ?? '-',
                'durasi'          => $t->durasi          ?? '-',
                'unggulan'        => $t->unggulan ? 'Ya' : 'Tidak',
            ])->toArray();
        } catch (\Throwable $e) {
            return [['nama' => 'Tari Topeng Kelana'], ['nama' => 'Tari Sintren']];
        }
    }

    private function getJadwalLatihan(): array
    {
        try {
            $jadwalList = JadwalLatihan::where('aktif', true)->orderBy('urutan')->get();
            if ($jadwalList->isEmpty()) {
                return ['Catatan' => 'Jadwal latihan belum diatur. Hubungi sanggar untuk info terbaru.'];
            }
            $result = [];
            foreach ($jadwalList as $j) {
                $result[] = [
                    'hari'       => $j->hari,
                    'jam'        => ($j->jam_mulai ?? '-') . ' – ' . ($j->jam_selesai ?? '-') . ' WIB',
                    'kelas'      => $j->kelas  ?? '-',
                    'tempat'     => $j->tempat ?? 'Sanggar',
                ];
            }
            return $result;
        } catch (\Throwable $e) {
            return [['keterangan' => 'Jadwal tidak dapat dimuat saat ini.']];
        }
    }

    private function getPelatihList(): array
    {
        try {
            $pelatihList = Pelatih::where('aktif', true)->orderBy('urutan')->get();
            if ($pelatihList->isEmpty()) {
                return [];
            }
            return $pelatihList->map(fn($p) => [
                'nama'          => $p->nama,
                'jabatan'       => $p->jabatan       ?? '-',
                'spesialisasi'  => $p->spesialisasi  ?? '-',
                'pengalaman'    => $p->pengalaman    ?? '-',
                'bio'           => $p->bio           ?? '-',
            ])->toArray();
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function getEventMendatang(): array
    {
        try {
            $events = Event::where('status', 'akan_datang')
                ->orderBy('tanggal')
                ->limit(5)
                ->get();
            if ($events->isEmpty()) {
                return [];
            }
            return $events->map(fn($e) => [
                'nama'      => $e->nama,
                'tanggal'   => Carbon::parse($e->tanggal)->translatedFormat('d F Y'),
                'lokasi'    => $e->lokasi    ?? '-',
                'kategori'  => $e->kategori  ?? '-',
                'deskripsi' => $e->deskripsi ?? '-',
                'berbayar'  => $e->is_berbayar ? 'Ya, tiket: Rp ' . number_format($e->harga_tiket ?? 0, 0, ',', '.') : 'Gratis',
            ])->toArray();
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function getEventSelesai(): array
    {
        try {
            $events = Event::where('status', 'selesai')
                ->orderByDesc('tanggal')
                ->limit(5)
                ->get();
            if ($events->isEmpty()) {
                return [];
            }
            return $events->map(fn($e) => [
                'nama'        => $e->nama,
                'tanggal'     => Carbon::parse($e->tanggal)->format('d/m/Y'),
                'lokasi'      => $e->lokasi      ?? '-',
                'kategori'    => $e->kategori    ?? '-',
                'penghargaan' => is_array($e->penghargaan) ? implode(', ', $e->penghargaan) : ($e->penghargaan ?? '-'),
                'hasil'       => $e->hasil        ?? '-',
            ])->toArray();
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function getEventMidhangSore(): array
    {
        try {
            $events = Event::where('kategori', 'midhang_sore')
                ->where('status', '!=', 'selesai')
                ->where('status', '!=', 'pending_approval')
                ->whereDate('tanggal', '>=', now())
                ->orderBy('tanggal')
                ->limit(5)
                ->get();
            if ($events->isEmpty()) {
                return [];
            }
            return $events->map(fn($e) => [
                'nama'      => $e->nama,
                'tanggal'   => Carbon::parse($e->tanggal)->format('d/m/Y'),
                'lokasi'    => $e->lokasi    ?? 'Sanggar Mulya Bhakti',
                'deskripsi' => $e->deskripsi ?? '-',
                'berbayar'  => $e->is_berbayar ? 'Ya, tiket: Rp ' . number_format($e->harga_tiket ?? 0, 0, ',', '.') : 'Gratis',
            ])->toArray();
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function getPengelolaList(): array
    {
        try {
            $pengelolaList = Pengelola::where('aktif', true)->orderBy('urutan')->get();
            if ($pengelolaList->isEmpty()) {
                return [];
            }
            return $pengelolaList->map(fn($p) => [
                'nama'    => $p->nama,
                'jabatan' => $p->jabatan ?? '-',
            ])->toArray();
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function getTopengList(): array
    {
        try {
            $topengList = Topeng::where('aktif', true)->orderBy('urutan')->get();
            if ($topengList->isEmpty()) {
                return [];
            }
            return $topengList->map(fn($t) => [
                'nama'     => $t->nama,
                'warna'    => $t->warna    ?? '-',
                'karakter' => $t->karakter ?? '-',
                'filosofi' => $t->filosofi ?? '-',
                'deskripsi'=> $t->deskripsi ?? '-',
            ])->toArray();
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function getPengumumanTerbaru(): array
    {
        try {
            $pengumumanList = Pengumuman::latest()->limit(5)->get();
            if ($pengumumanList->isEmpty()) {
                return [];
            }
            return $pengumumanList->map(fn($p) => [
                'judul'   => $p->judul,
                'tipe'    => $p->tipe   ?? '-',
                'konten'  => $p->konten ?? '-',
                'tanggal' => Carbon::parse($p->created_at)->format('d/m/Y'),
            ])->toArray();
        } catch (\Throwable $e) {
            return [];
        }
    }

    // =========================================================================
    // SECTION 2: PEMBANGUN SYSTEM INSTRUCTION
    // Mengumpulkan semua data dan menyusunnya menjadi instruksi AI yang kuat.
    // =========================================================================

    private function buildSystemInstruction(): string
    {
        // Kumpulkan semua data dari database
        $profil       = $this->getProfilSanggar();
        $tarian       = $this->getTarianList();
        $jadwal       = $this->getJadwalLatihan();
        $pelatih      = $this->getPelatihList();
        $pengelola    = $this->getPengelolaList();
        $topeng       = $this->getTopengList();
        $eventDatang  = $this->getEventMendatang();
        $eventSelesai = $this->getEventSelesai();
        $midhangSore  = $this->getEventMidhangSore();
        $pengumuman   = $this->getPengumumanTerbaru();

        // Susun knowledge base lengkap dalam format JSON terstruktur
        // PENTING: Array kosong diganti string keterangan agar AI tidak kebingungan
        $knowledgeBase = [
            'profil_sanggar'        => $profil,
            'tarian_yang_diajarkan' => $tarian    ?: 'DATA_KOSONG: Belum ada data tarian yang terdaftar di sistem.',
            'koleksi_topeng_pancawanda' => $topeng ?: 'DATA_KOSONG: Belum ada data topeng yang terdaftar di sistem.',
            'jadwal_latihan'        => $jadwal    ?: 'DATA_KOSONG: Belum ada jadwal latihan yang terdaftar di sistem.',
            'daftar_pelatih'        => $pelatih   ?: 'DATA_KOSONG: Belum ada data pelatih yang terdaftar di sistem.',
            'daftar_pengelola'      => $pengelola ?: 'DATA_KOSONG: Belum ada data pengelola yang terdaftar di sistem.',
            'event_mendatang_umum'  => $eventDatang  ?: 'DATA_KOSONG: Belum ada event mendatang yang dijadwalkan.',
            'event_midhang_sore_mendatang' => $midhangSore ?: 'DATA_KOSONG: Belum ada jadwal Midhang Sore mendatang.',
            'event_selesai_terbaru' => $eventSelesai ?: 'DATA_KOSONG: Belum ada riwayat event yang tercatat.',
            'pengumuman_terbaru'    => $pengumuman  ?: 'DATA_KOSONG: Tidak ada pengumuman terbaru saat ini.',
            'informasi_pendaftaran' => [
                'cara_daftar'  => 'Pendaftaran bisa dilakukan melalui website sanggar atau langsung datang ke sanggar.',
                'biaya'        => 'Pendaftaran anggota gratis. Calon anggota dapat memilih kelas tari setelah mendaftar.',
                'persyaratan'  => 'Terbuka untuk semua umur. Tidak diperlukan pengalaman menari sebelumnya.',
            ],
            'penjelasan_program_kegiatan' => [
                'midhang_sore' => [
                    'nama'        => 'Midhang Sore',
                    'penjelasan'  => 'Midhang Sore adalah program platform kolaborasi terbuka di Sanggar Mulya Bhakti. Ini adalah wadah bagi seniman, koreografer, dan penggiat budaya dari luar sanggar untuk berbagi ilmu melalui workshop atau kelas khusus. Pengajuan dilakukan melalui formulir di halaman Event website, dan setelah disetujui admin, acara akan tayang di jadwal sanggar.',
                    'syarat'      => ['Karya/Workshop berkaitan dengan seni pertunjukan (Tari, Musik, Teater)', 'Memiliki portofolio yang jelas', 'Jadwal pelaksanaan didiskusikan lebih lanjut'],
                    'ujian'       => 'Midhang Sore juga berfungsi sebagai ujian kenaikan tingkat bagi anggota tetap sanggar. Anggota tetap dengan kehadiran minimal 75% dapat mendaftar ujian Midhang Sore untuk naik tingkat.',
                    'fasilitas'   => 'Aula luas & sound system, audiens 100+ anggota aktif',
                ],
                'studi_budaya' => [
                    'nama'        => 'Studi Budaya',
                    'penjelasan'  => 'Studi Budaya adalah kegiatan tahunan berupa penelusuran dan pembelajaran mendalam mengenai kebudayaan spesifik yang diselenggarakan oleh Sanggar Mulya Bhakti.',
                ],
                'pagelaran' => [
                    'nama'        => 'Pagelaran',
                    'penjelasan'  => 'Pagelaran adalah pentas seni akbar dan pertunjukan puncak yang menampilkan karya-karya terbaik dari anggota Sanggar Mulya Bhakti.',
                ],
            ],
        ];

        // Konversi ke JSON yang bersih dan terbaca
        $jsonKnowledge = json_encode($knowledgeBase, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $namaAsisten   = $profil['nama'] ?? 'Sanggar Mulya Bhakti';
        $tanggalHariIni = Carbon::now()->format('l, d/m/Y');

        return <<<INSTRUCTION
Kamu adalah "Asisten {$namaAsisten}", asisten virtual resmi untuk sanggar tari ini.
Tanggal hari ini adalah: {$tanggalHariIni}.

=== BASIS DATA RESMI SANGGAR (KNOWLEDGE BASE) ===
Semua informasi resmi tentang sanggar ada di dalam JSON berikut ini.
Kamu HARUS selalu merujuk ke JSON ini saat menjawab. Jangan mengarang data apapun.

{$jsonKnowledge}

=== CARA MEMBACA DATA ===
- Jika nilai sebuah field adalah string yang diawali "DATA_KOSONG:", artinya data itu memang belum diisi admin ke sistem.
- Jika nilai adalah "-", artinya informasi tersebut belum tersedia.
- Jika nilai berisi data nyata (nama, angka, teks), gunakan data itu untuk menjawab.

=== ATURAN KRITIS (TIDAK BOLEH DILANGGAR) ===

**ATURAN #1 – WAJIB JAWAB SEMUA PERTANYAAN TENTANG SANGGAR:**
Jika pengguna bertanya tentang topik apapun yang ada di knowledge base, kamu WAJIB menjawab secara langsung.
Topik yang HARUS dijawab meliputi:
- profil, sejarah, visi, misi, lokasi, kontak, statistik sanggar
- tarian yang diajarkan (nama, deskripsi, asal, fungsi, kostum, dll)
- topeng Pancawanda (nama, warna, karakter, filosofi, deskripsi tiap topeng)
- jadwal latihan (hari, jam, kelas, tempat)
- pelatih sanggar (nama, jabatan, spesialisasi)
- pengelola / pengurus sanggar (nama, jabatan/posisi)
- event mendatang maupun yang sudah selesai
- program Midhang Sore (apa itu, syarat, ujian, cara daftar)
- program Studi Budaya dan Pagelaran
- pengumuman terbaru
- cara dan persyaratan pendaftaran anggota

JANGAN memberikan jawaban pengalihan seperti "silakan tanyakan seputar..." atau "untuk info lebih lanjut..." jika topiknya memang ada di knowledge base.

Contoh SALAH (jangan lakukan ini):
- User tanya: "Siapa pelatih sanggar?"
- Jawaban SALAH: "Terima kasih! Untuk info lebih lanjut, silakan tanyakan seputar jadwal atau tarian kami."
- Jawaban BENAR: "Saat ini data pelatih belum terdaftar di sistem kami 🙏. Untuk info lebih lanjut, hubungi sanggar langsung melalui kontak yang tersedia."

**ATURAN #2 – DATA KOSONG = JUJUR, BUKAN DIALIHKAN:**
Jika data yang ditanya tertulis "DATA_KOSONG:" atau "-" di JSON, katakan dengan jujur bahwa data belum tersedia di sistem, lalu arahkan ke kontak sanggar. Jangan mengalihkan ke topik lain.

**ATURAN #3 – TOPIK DI LUAR SANGGAR:**
Hanya tolak dan alihkan jika pertanyaan BENAR-BENAR tidak berhubungan dengan sanggar (misal: politik, olahraga, teknologi umum). Untuk semua hal tentang sanggar, HARUS dijawab.

**ATURAN #4 – KONTAK RESMI:**
Gunakan HANYA kontak yang ada di dalam JSON (profil_sanggar.kontak). Jangan karang nomor atau akun lain.

=== PANDUAN FORMAT JAWABAN ===
- Gunakan Bahasa Indonesia yang sopan, ramah, dan mudah dipahami.
- Jawab ringkas dan jelas (maksimal 250 kata).
- Gunakan format daftar/poin jika ada banyak data (misal daftar topeng, daftar pengelola).
- Gunakan emoji secukupnya agar terasa ramah 🎭✨
- Perhatikan tanggal hari ini ({$tanggalHariIni}) untuk konteks event mendatang vs sudah lewat.

INSTRUCTION;
    }

    // =========================================================================
    // SECTION 3: PENGIRIM PESAN KE GEMINI API
    // =========================================================================

    /**
     * Kirim pesan ke Gemini API dengan system instruction yang kaya data.
     *
     * @param string $message  Pesan dari pengguna
     * @param array  $history  Riwayat percakapan [['role' => 'user'|'model', 'content' => '...']]
     * @return string          Balasan dari AI
     */
    public function generateResponse(string $message, array $history = []): string
    {
        $systemInstruction = $this->buildSystemInstruction();

        return $this->isLiteLLM
            ? $this->callLiteLLM($message, $history, $systemInstruction)
            : $this->callGoogleApi($message, $history, $systemInstruction);
    }

    /**
     * Panggil via LiteLLM / OpenAI-compatible proxy (format: /chat/completions)
     */
    private function callLiteLLM(string $message, array $history, string $systemInstruction): string
    {
        $messages = [
            ['role' => 'system', 'content' => $systemInstruction],
        ];

        foreach ($history as $hist) {
            $messages[] = [
                'role'    => $hist['role'] === 'user' ? 'user' : 'assistant',
                'content' => $hist['content'],
            ];
        }

        $messages[] = ['role' => 'user', 'content' => $message];

        $requestBody = [
            'model'       => $this->model,
            'messages'    => $messages,
            'temperature' => 0.2,
            'max_tokens'  => 600,
        ];

        $ch = curl_init($this->apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey,
            ],
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($requestBody),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT        => 30,
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        if (!$response) {
            throw new Exception('Tidak dapat terhubung ke LiteLLM proxy. Periksa koneksi internet.');
        }

        $result = json_decode($response, true);

        if (isset($result['error'])) {
            throw new Exception('LiteLLM Error: ' . (is_array($result['error']) ? ($result['error']['message'] ?? json_encode($result['error'])) : $result['error']));
        }

        $reply = $result['choices'][0]['message']['content'] ?? null;

        if (!$reply) {
            return 'Maaf, saya sedang mengalami gangguan teknis. Silakan coba lagi atau hubungi kami langsung.';
        }

        return $reply;
    }

    /**
     * Panggil via Google Gemini API langsung (format native Gemini)
     */
    private function callGoogleApi(string $message, array $history, string $systemInstruction): string
    {
        $contents = [];

        foreach ($history as $hist) {
            $contents[] = [
                'role'  => $hist['role'] === 'user' ? 'user' : 'model',
                'parts' => [['text' => $hist['content']]],
            ];
        }

        $contents[] = [
            'role'  => 'user',
            'parts' => [['text' => $message]],
        ];

        $requestBody = [
            'contents' => $contents,
            'systemInstruction' => [
                'parts' => [['text' => $systemInstruction]]
            ],
            'generationConfig' => [
                'temperature'     => 0.2,
                'topP'            => 0.80,
                'topK'            => 40,
                'maxOutputTokens' => 600,
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
            CURLOPT_POSTFIELDS     => json_encode($requestBody),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT        => 30,
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        if (!$response) {
            throw new Exception('Tidak dapat terhubung ke Gemini API. Periksa koneksi internet.');
        }

        $result = json_decode($response, true);

        if (isset($result['error'])) {
            throw new Exception('Gemini Error: ' . $result['error']['message']);
        }

        $reply = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if (!$reply) {
            $finishReason = $result['candidates'][0]['finishReason'] ?? '';
            if ($finishReason === 'SAFETY') {
                return "Maaf, saya tidak dapat menjawab pertanyaan tersebut. Silakan tanyakan seputar {$this->getNamaSanggar()} ya! 😊";
            }
            return 'Maaf, saya sedang mengalami gangguan teknis. Silakan coba lagi atau hubungi kami langsung.';
        }

        return $reply;
    }

    /**
     * Helper: Ambil nama sanggar untuk pesan error.
     */
    private function getNamaSanggar(): string
    {
        try {
            return SanggarProfile::getInstance()->nama_sanggar ?? 'Sanggar Mulya Bhakti';
        } catch (\Throwable $e) {
            return 'Sanggar Mulya Bhakti';
        }
    }
}