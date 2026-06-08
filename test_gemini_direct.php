<?php
// Test Gemini API dengan model terbaru
$apiKey = 'AIzaSyCZbKnXlRMq3Xzb_RL5NGTyUAu00S6mvNo';
$model  = 'gemini-2.0-flash';
$url    = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

$systemPrompt = "Kamu adalah Asisten Sanggar, asisten virtual resmi Sanggar Mulya Bhakti - sanggar seni tari tradisional dari Indramayu, Jawa Barat. Hanya jawab pertanyaan seputar sanggar.";

$data = [
    'contents' => [
        [
            'role'  => 'user',
            'parts' => [['text' => $systemPrompt]],
        ],
        [
            'role'  => 'model',
            'parts' => [['text' => 'Baik, saya siap membantu sebagai Asisten Sanggar Mulya Bhakti!']],
        ],
        [
            'role'  => 'user',
            'parts' => [['text' => 'Halo! Apa saja tarian yang diajarkan?']],
        ],
    ],
    'generationConfig' => [
        'temperature'     => 0.3,
        'maxOutputTokens' => 200,
    ],
];

echo "=== Test Gemini API (model: {$model}) ===\n\n";

$ch = curl_init($url);
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
$curlErr  = curl_error($ch);
curl_close($ch);

echo "HTTP Code: {$httpCode}\n";

if ($curlErr) {
    echo "CURL Error: {$curlErr}\n";
    exit(1);
}

$result = json_decode($response, true);

if (isset($result['error'])) {
    echo "❌ API Error: " . $result['error']['message'] . "\n";
    exit(1);
}

$reply = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;
if ($reply) {
    echo "✅ Gemini API BERJALAN!\n\n";
    echo "Reply: " . trim($reply) . "\n";
} else {
    echo "⚠️ Response kosong. Full response:\n";
    echo json_encode($result, JSON_PRETTY_PRINT) . "\n";
}
