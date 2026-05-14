<?php
require __DIR__.'/vendor/autoload.php';
use Illuminate\Support\Facades\Http;

// Mock Laravel environment enough to run Http
$apiKey = 'AIzaSyCiKhxm2Fim56WcVMKlQEJwkFcJxV3Cx9s';
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key={$apiKey}";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'contents' => [
        ['parts' => [['text' => 'Halo, siapa namamu?']]]
    ]
]));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$err = curl_error($ch);
curl_close($ch);

if ($err) {
    echo "CURL Error: " . $err;
} else {
    echo "Response: " . $response;
}
