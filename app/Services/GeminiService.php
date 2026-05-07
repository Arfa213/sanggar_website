<?php

namespace App\Services;

use Gemini;
use Exception;

class GeminiService
{
    protected $client;

    public function __construct()
    {
        // Mengambil API Key dari config atau .env
        $apiKey = config('app.gemini_api_key') ?? env('GEMINI_API_KEY');
        
        if (!$apiKey) {
            throw new Exception('Gemini API Key tidak ditemukan. Cek file .env Anda.');
        }

        $this->client = Gemini::client($apiKey);
    }

    /**
     * Fungsi utama untuk mengirim pesan ke AI
     */
    public function generateResponse(string $message)
{
    $apiKey = env('GEMINI_API_KEY');
    
    // SESUAIKAN DENGAN DAFTAR TADI: gemini-2.5-flash
    $modelName = "gemini-2.5-flash"; 
    
    $url = "https://generativelanguage.googleapis.com/v1beta/models/{$modelName}:generateContent?key=" . $apiKey;

    $data = [
    // --- INI KUNCINYA ---
    "system_instruction" => [
        "parts" => [
            ["text" => "Nama kamu adalah Asisten Sanggar. Kamu adalah asisten virtual resmi untuk Sanggar Tari Tradisional Indramayu. Jika ditanya nama sanggar, jawab bahwa ini adalah Sanggar Mulya Bhakti yang fokus pada pelestarian tari topeng. Jangan memberikan nomor telepon atau email selain yang diperintahkan. Gunakan gaya bahasa yang ramah dan membantu."]
        ]
    ],
    // --------------------
    "contents" => [
        [
            "parts" => [
                ["text" => $message]
            ]
        ]
    ],
    // Tambahkan ini supaya jawaban lebih konsisten (tidak ngawur)
    "generationConfig" => [
        "temperature" => 0.7, 
        "topP" => 0.95,
        "topK" => 64,
        "maxOutputTokens" => 1024,
    ]
];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

    $response = curl_exec($ch);
    $result = json_decode($response, true);
    curl_close($ch);

    if (isset($result['error'])) {
        throw new \Exception("Google Error: " . $result['error']['message']);
    }

    // Ambil jawaban AI
    return $result['candidates'][0]['content']['parts'][0]['text'] ?? "AI tidak memberikan respon.";
}
}