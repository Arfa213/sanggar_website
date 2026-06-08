<?php
// Test semua model satu per satu sampai ada yang jalan
$apiKey = 'AIzaSyCZbKnXlRMq3Xzb_RL5NGTyUAu00S6mvNo';

$modelsToTry = [
    'gemini-2.0-flash-lite',
    'gemini-2.0-flash-lite-001',
    'gemini-2.5-flash',
    'gemini-flash-latest',
    'gemini-flash-lite-latest',
];

$data = [
    'contents' => [[
        'role'  => 'user',
        'parts' => [['text' => 'Halo, jawab singkat: siapa kamu?']],
    ]],
    'generationConfig' => [
        'temperature'     => 0.3,
        'maxOutputTokens' => 50,
    ],
];

foreach ($modelsToTry as $model) {
    $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
    echo "Testing: {$model} ... ";

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($data),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT        => 15,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $result = json_decode($response, true);

    if ($httpCode === 200 && isset($result['candidates'][0]['content']['parts'][0]['text'])) {
        echo "✅ BERHASIL! HTTP {$httpCode}\n";
        echo "Reply: " . trim($result['candidates'][0]['content']['parts'][0]['text']) . "\n";
        echo "\n>>> MODEL YANG BISA DIPAKAI: {$model} <<<\n";
        break;
    } elseif (isset($result['error'])) {
        $code = $result['error']['code'];
        $status = $result['error']['status'];
        echo "❌ HTTP {$httpCode} - {$status} (Code: {$code})\n";
    } else {
        echo "⚠️ HTTP {$httpCode} - Unexpected\n";
    }
    
    sleep(1); // Jeda antar model
}
