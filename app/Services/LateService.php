<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class LateService
{
    private string $apiKey;
    private string $apiUrl;
    private string $profileId;

    public function __construct()
    {
        $this->apiKey    = Setting::get('late_api_key', config('services.late.api_key'));
        $this->apiUrl    = config('services.late.api_url');
        $this->profileId = Setting::get('late_profile_id', config('services.late.profile_id'));
    }

    /**
     * Send story to Late API platforms.
     *
     * @param array  $platforms    e.g. ['instagram_story', 'facebook_story', 'tiktok_story']
     * @param string $mediaPath   The relative path stored in DB (e.g. 'status/filename.jpg')
     * @param string $caption
     * @return array ['success' => bool, 'message' => string]
     */
    public function sendStory(array $platforms, string $mediaPath, string $caption): array
    {
        if (empty($platforms)) {
            return ['success' => false, 'message' => 'No platforms specified'];
        }

        // 1. Get Accounts from Late API filtered by profileId
        $accounts = $this->getAccounts();

        Log::info("Late API: Found " . count($accounts) . " accounts", [
            'accounts'   => collect($accounts)->map(fn($a) => ['_id' => $a['_id'] ?? null, 'platform' => $a['platform'] ?? null])->toArray(),
            'profileId'  => $this->profileId,
        ]);

        if (empty($accounts)) {
            return ['success' => false, 'message' => 'No accounts found in your Late profile. Please connect accounts first at getlate.dev'];
        }

        $postPlatforms = [];
        foreach ($platforms as $platform) {
            // Mapping: instagram_story -> instagram, facebook_story -> facebook, tiktok_story -> tiktok
            $apiPlatform = str_replace('_story', '', $platform);
            // Also handle whatsapp_status -> whatsapp
            $apiPlatform = str_replace('_status', '', $apiPlatform);

            $account = collect($accounts)->first(fn($a) => ($a['platform'] ?? '') === $apiPlatform);

            if ($account) {
                $postPlatforms[] = [
                    'platform'  => $apiPlatform,
                    'accountId' => $account['_id'],
                ];
            } else {
                Log::warning("No account found for platform: {$apiPlatform}", [
                    'available_platforms' => collect($accounts)->pluck('platform')->toArray()
                ]);
            }
        }

        if (empty($postPlatforms)) {
            return ['success' => false, 'message' => 'No connected accounts matched the selected platforms. Available: ' . collect($accounts)->pluck('platform')->implode(', ')];
        }

        // 2. Upload media first, then create the post
        return $this->createPost($postPlatforms, $mediaPath, $caption);
    }

    private function getAccounts(): array
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(30)
                ->get("{$this->apiUrl}/accounts", [
                    'profileId' => $this->profileId,
                ]);

            Log::info("Late API getAccounts response", [
                'status' => $response->status(),
                'body'   => substr($response->body(), 0, 500),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                // Response could be { accounts: [...] } or just [...]
                return $data['accounts'] ?? $data['data'] ?? (is_array($data) && isset($data[0]) ? $data : []);
            }

            Log::error("Late API error fetching accounts", [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return [];
        } catch (\Exception $e) {
            Log::error("Late API exception fetching accounts: " . $e->getMessage());
            return [];
        }
    }

    private function createPost(array $postPlatforms, string $mediaPath, string $caption): array
    {
        try {
            // 1. Upload local media to Late API storage (via presigned URL)
            $uploadedUrl = $this->uploadMediaToLate($mediaPath);

            if (!$uploadedUrl) {
                return ['success' => false, 'message' => 'Failed to upload media to Late storage.'];
            }

            // 2. Prepare Platforms array and Media Items
            $isVideo = str_contains($mediaPath, '.mp4') || str_contains($mediaPath, '.mov');
            $mediaType = $isVideo ? 'video' : 'image';
            
            $platforms = [];
            $hasTiktok = false;
            foreach ($postPlatforms as $pp) {
                $target = [
                    'platform'  => $pp['platform'],
                    'accountId' => $pp['accountId'],
                    'platformSpecificData' => [],
                ];

                // Instagram Story
                if ($pp['platform'] === 'instagram') {
                    $target['platformSpecificData'] = [
                        'contentType' => 'story',
                        'publish_to_story' => true, // Fallback common parameter
                    ];
                }

                // Facebook Story
                if ($pp['platform'] === 'facebook') {
                    $target['platformSpecificData'] = [
                        'contentType' => 'story',
                    ];
                }

                // TikTok Story (Attempt)
                if ($pp['platform'] === 'tiktok') {
                    $hasTiktok = true;
                    $target['platformSpecificData'] = [
                        'contentType' => 'story',
                    ];
                    // Also some versions put settings in its own object
                    $target['tiktokSettings'] = [
                        'content_preview_confirmed' => true,
                        'express_consent_given'     => true,
                        'privacy_level'             => 'PUBLIC_TO_EVERYONE',
                        'contentType'               => 'story',
                    ];
                }

                $platforms[] = $target;
            }

            // 3. Create post via /posts endpoint
            $payload = [
                'content'    => $caption,
                'mediaItems' => [
                    [
                        'url'  => $uploadedUrl,
                        'type' => $mediaType,
                    ]
                ],
                'platforms'  => $platforms,
                'publishNow' => true,
            ];

            // TikTok Top-Level Settings (Fallback)
            if ($hasTiktok) {
                $payload['tiktokSettings'] = [
                    'content_preview_confirmed' => true,
                    'express_consent_given'     => true,
                    'privacy_level'             => 'PUBLIC_TO_EVERYONE',
                    'contentType'               => 'story',
                ];
            }

            Log::info("Late API createPost payload", $payload);

            $response = Http::withToken($this->apiKey)
                ->timeout(60)
                ->post("{$this->apiUrl}/posts", $payload);

            if ($response->successful()) {
                Log::info("Late API Success: Post created", $response->json());
                return ['success' => true, 'message' => 'Post successfully sent to Late API.'];
            }

            Log::error("Late API error creating post", [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => "Failed to create post: " . ($response->json()['error'] ?? $response->json()['message'] ?? $response->body()),
            ];
        } catch (\Exception $e) {
            Log::error("Late API exception: " . $e->getMessage());
            return ['success' => false, 'message' => "Exception: " . $e->getMessage()];
        }
    }

    /**
     * Upload media file from local storage to Late API via presigned URL.
     *
     * @param string $mediaPath The relative path in the 'public' disk (e.g. 'status/filename.jpg')
     * @return string|null  The public URL of the uploaded file on Late, or null on failure
     */
    private function uploadMediaToLate(string $mediaPath): ?string
    {
        try {
            // Resolve local file path directly from the public disk
            $localPath = Storage::disk('public')->path($mediaPath);

            Log::info("Late Upload: Resolving media", [
                'mediaPath' => $mediaPath,
                'localPath' => $localPath,
                'exists'    => file_exists($localPath),
            ]);

            if (!file_exists($localPath)) {
                Log::error("Local file not found for upload to Late: " . $localPath);
                return null;
            }

            $filename = basename($localPath);
            $mimeType = mime_content_type($localPath) ?: 'application/octet-stream';

            // A. Request Presigned URL from Late API
            $presignResponse = Http::withToken($this->apiKey)
                ->timeout(30)
                ->post("{$this->apiUrl}/media/presign", [
                    'filename'    => $filename,
                    'contentType' => $mimeType,
                ]);

            Log::info("Late Presign response", [
                'status' => $presignResponse->status(),
                'body'   => substr($presignResponse->body(), 0, 500),
            ]);

            if (!$presignResponse->successful()) {
                Log::error("Late API Presign error", ['body' => $presignResponse->body()]);
                return null;
            }

            $presignData = $presignResponse->json();
            $uploadUrl   = $presignData['uploadUrl'] ?? null;
            $publicUrl   = $presignData['publicUrl'] ?? null;

            if (!$uploadUrl || !$publicUrl) {
                Log::error("Late Presign missing uploadUrl or publicUrl", $presignData);
                return null;
            }

            // B. PUT binary to the presigned uploadUrl
            $fileContent = file_get_contents($localPath);
            $putResponse = Http::withHeaders(['Content-Type' => $mimeType])
                ->timeout(120)
                ->withBody($fileContent, $mimeType)
                ->put($uploadUrl);

            if (!$putResponse->successful()) {
                Log::error("Late API PUT upload error", [
                    'status' => $putResponse->status(),
                    'body'   => substr($putResponse->body(), 0, 300),
                ]);
                return null;
            }

            Log::info("Late Upload success: {$publicUrl}");
            return $publicUrl;
        } catch (\Exception $e) {
            Log::error("Late API Media Upload Exception: " . $e->getMessage());
            return null;
        }
    }
}
