<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class WhatsAppService
{
    /**
     * Send WhatsApp Status via Local Node.js Bridge (Baileys).
     *
     * @param string $mediaPath  The relative path in the 'public' disk
     * @param string $caption
     * @return array ['success' => bool, 'message' => string]
     */
    public function sendStatus(string $mediaPath, string $caption): array
    {
        try {
            $localPath = Storage::disk('public')->path($mediaPath);
            $bridgeUrl = 'http://127.0.0.1:3000/send-status';

            if (!file_exists($localPath)) {
                Log::error("WA Bridge: Local file not found: " . $localPath);
                return ['success' => false, 'message' => 'File media tidak ditemukan: ' . $mediaPath];
            }

            // Call Node.js Bridge
            $response = \Illuminate\Support\Facades\Http::timeout(120)
                ->post($bridgeUrl, [
                    'filePath' => $localPath,
                    'caption'  => $caption
                ]);

            if ($response->successful()) {
                Log::info('WA Bridge: WhatsApp Status sent successfully');
                return ['success' => true, 'message' => 'Status terkirim melalui Bridge'];
            }

            Log::error('WA Bridge error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => 'WA Bridge Error: ' . ($response->json()['message'] ?? 'Bridge Responded with error'),
            ];

        } catch (\Exception $e) {
            Log::error('WA Bridge exception: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Bridge Exception: Pastikan Node bridge di port 3000 aktif.'];
        }
    }
}
