<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$request = new \Illuminate\Http\Request();
$request->merge([
    'message' => 'Halo',
    'session_id' => 'test_123'
]);

// Run the controller logic manually to see where it goes wrong
$sessionId = $request->session_id;
$userId    = 1;
$apiKey    = config('services.gemini.key');

$profil  = \App\Models\SanggarProfile::getInstance();
$tarian  = \App\Models\Tarian::where('aktif', true)->pluck('nama')->implode(', ');
$context = "Kamu adalah asisten virtual Sanggar Mulya Bhakti, sanggar seni tari tradisional dari Indramayu, Jawa Barat. " .
    "Informasi sanggar: {$profil->nama_sanggar}, berdiri sejak {$profil->tahun_berdiri}. " .
    "Tarian yang diajarkan: {$tarian}. " .
    "Kontak: {$profil->no_hp}, {$profil->email}. " .
    "Selalu jawab dengan sopan, dalam Bahasa Indonesia, dan fokus pada informasi sanggar. " .
    "Jika ditanya di luar konteks sanggar, arahkan kembali ke topik sanggar.";

$contents = [];
$contents[] = [
    'role'  => 'user',
    'parts' => [['text' => $context]],
];
$contents[] = [
    'role'  => 'model',
    'parts' => [['text' => 'Baik, saya siap membantu menjawab pertanyaan seputar Sanggar Mulya Bhakti!']],
];
$contents[] = [
    'role'  => 'user',
    'parts' => [['text' => $request->message]],
];

try {
    $response = \Illuminate\Support\Facades\Http::withoutVerifying()
        ->timeout(30)
        ->post(
            "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}",
            ['contents' => $contents]
        );

    echo "Status: " . $response->status() . "\n";
    echo "Body: " . $response->body() . "\n";
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
