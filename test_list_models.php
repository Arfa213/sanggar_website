<?php
$key = 'AIzaSyCZbKnXlRMq3Xzb_RL5NGTyUAu00S6mvNo';
$ch = curl_init('https://generativelanguage.googleapis.com/v1beta/models?key=' . $key);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$res = curl_exec($ch);
curl_close($ch);
$data = json_decode($res, true);

echo "=== Models yang support generateContent ===\n";
foreach ($data['models'] as $m) {
    if (in_array('generateContent', $m['supportedGenerationMethods'] ?? [])) {
        echo $m['name'] . "\n";
    }
}
