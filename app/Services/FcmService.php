<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmService
{
    private function getAccessToken()
    {
        $keyFilePath = storage_path('app/firebase-service-account.json');
        if (!file_exists($keyFilePath)) {
            Log::warning('Firebase service account file not found. Push notifications will fail.');
            return null;
        }

        try {
            $client = new GoogleClient();
            $client->setAuthConfig($keyFilePath);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
            $res = $client->fetchAccessTokenWithAssertion();
            return $res['access_token'] ?? null;
        } catch (\Exception $e) {
            Log::error("FCM getAccessToken Exception: " . $e->getMessage());
            return null;
        }
    }

    private function getProjectId()
    {
        $keyFilePath = storage_path('app/firebase-service-account.json');
        if (!file_exists($keyFilePath)) return null;

        $config = json_decode(file_get_contents($keyFilePath), true);
        return $config['project_id'] ?? null;
    }

    public function sendNotification($target, $title, $body, array $data = [], bool $isTopic = false)
    {
        $accessToken = $this->getAccessToken();
        $projectId = $this->getProjectId();

        if (!$accessToken || !$projectId) {
            Log::warning("FCM Skip sending notification: Token or Project ID is null.");
            return false;
        }

        $message = [
            'notification' => [
                'title' => $title,
                'body' => $body
            ],
            'data' => array_merge([
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ], $data)
        ];

        if ($isTopic) {
            $message['topic'] = $target;
        } else {
            $message['token'] = $target;
        }

        // Android specific configurations for high priority and sound
        $message['android'] = [
            'priority' => 'high',
            'notification' => [
                'sound' => 'default',
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                'channel_id' => 'sanggar_pengumuman'
            ]
        ];

        // APNS (iOS) specific configurations for high priority and sound
        $message['apns'] = [
            'headers' => [
                'apns-priority' => '10'
            ],
            'payload' => [
                'aps' => [
                    'sound' => 'default',
                    'badge' => 1
                ]
            ]
        ];

        $payload = ['message' => $message];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", $payload);

            if ($response->successful()) {
                return true;
            } else {
                Log::error("FCM sendNotification Error: " . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error("FCM sendNotification Exception: " . $e->getMessage());
            return false;
        }
    }

    public function sendToToken($token, $title, $body, array $data = [])
    {
        return $this->sendNotification($token, $title, $body, $data, false);
    }

    public function sendToTopic($topic, $title, $body, array $data = [])
    {
        return $this->sendNotification($topic, $title, $body, $data, true);
    }
}
