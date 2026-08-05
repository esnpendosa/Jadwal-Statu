<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Services\LateService;
use App\Services\WhatsAppService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PublishStatus extends Command
{
    protected $signature   = 'status:publish';
    protected $description = 'Publish scheduled status/stories to social media platforms';

    public function handle(): int
    {
        $now = now();
        $this->info("Running status:publish at {$now->toDateTimeString()} ({$now->timezoneName})");
        Log::debug("Running status:publish at {$now->toDateTimeString()} ({$now->timezoneName})");

        $posts = Post::pending()->scheduledBefore($now)->get();

        if ($posts->isEmpty()) {
            $this->info('No pending posts to publish.');
            return self::SUCCESS;
        }

        $this->info("Found {$posts->count()} post(s) to publish.");
        Log::info("Found {$posts->count()} post(s) to publish.");

        $lateService      = app(LateService::class);
        $whatsAppService  = app(WhatsAppService::class);

        foreach ($posts as $post) {
            $this->info("Processing post ID: {$post->id} (Scheduled at: {$post->schedule_time})");
            $this->processPost($post, $lateService, $whatsAppService);
        }

        return self::SUCCESS;
    }

    private function processPost(Post $post, LateService $lateService, WhatsAppService $whatsAppService): void
    {
        $this->line("Processing post ID: {$post->id}");

        $platforms   = $post->platforms ?? [];
        $mediaPath   = $post->media_path;  // Raw disk path
        $caption     = $post->caption ?? '';
        $contentType = $post->content_type ?? 'story';

        if (!$mediaPath) {
            $this->warn("Post ID {$post->id} has no media, marking as failed.");
            $post->update(['status' => 'failed', 'error_message' => 'No media path available']);
            return;
        }

        $errors        = [];
        $successCount  = 0;
        $totalPlatforms = 0;

        // -- WhatsApp Status (uses Node.js bridge on port 3000) --
        if (in_array('whatsapp_status', $platforms)) {
            $totalPlatforms++;
            $result = $whatsAppService->sendStatus($mediaPath, $caption);
            if (!$result['success']) {
                // Check if it's a connection error (bridge not running)
                $isConnectionError = str_contains($result['message'], 'Bridge Exception') ||
                                     str_contains($result['message'], 'port 3000') ||
                                     str_contains($result['message'], 'connect');

                if ($isConnectionError) {
                    $this->warn("  ⚠ WhatsApp Bridge tidak aktif, dilewati: " . $result['message']);
                    // Don't count as hard failure — bridge may simply be offline
                    $errors[] = 'WhatsApp: ' . $result['message'];
                } else {
                    $this->error("  ✗ WhatsApp Status gagal: " . $result['message']);
                    $errors[] = 'WhatsApp: ' . $result['message'];
                    $totalPlatforms++; // Count as attempted
                }
            } else {
                $successCount++;
                $this->info("  ✓ WhatsApp Status sent.");
            }
        }

        // -- Late API: Story platforms (Instagram Story, Facebook Story, TikTok) --
        $lateStoryPlatforms = array_values(array_filter($platforms, fn($p) => in_array($p, [
            'instagram_story', 'facebook_story', 'tiktok_story'
        ])));

        if (!empty($lateStoryPlatforms)) {
            $totalPlatforms++;
            $result = $lateService->sendContent($lateStoryPlatforms, $mediaPath, $caption, 'story');
            if (!$result['success']) {
                $errors[] = 'Late API (Story): ' . $result['message'];
                $this->error("  ✗ Late API Story gagal: " . $result['message']);
            } else {
                $successCount++;
                $this->info("  ✓ Late API story posted: " . implode(', ', $lateStoryPlatforms));
            }
        }

        // -- Late API: Post platforms (Instagram Post, Facebook Post) --
        $latePostPlatforms = array_values(array_filter($platforms, fn($p) => in_array($p, [
            'instagram_post', 'facebook_post'
        ])));

        if (!empty($latePostPlatforms)) {
            $totalPlatforms++;
            $result = $lateService->sendContent($latePostPlatforms, $mediaPath, $caption, 'post');
            if (!$result['success']) {
                $errors[] = 'Late API (Post): ' . $result['message'];
                $this->error("  ✗ Late API Post gagal: " . $result['message']);
            } else {
                $successCount++;
                $this->info("  ✓ Late API post published: " . implode(', ', $latePostPlatforms));
            }
        }

        // Mark as posted if at least one platform group succeeded
        // (or if no platforms were selected but no hard errors)
        $hasLateSuccess = $successCount > 0;
        $onlyWaFailed   = !empty($errors) && $successCount > 0;

        if ($successCount > 0) {
            // At least one platform succeeded — mark as posted
            $errorNote = !empty($errors) ? ' (Catatan: ' . implode(' | ', $errors) . ')' : null;
            $post->update(['status' => 'posted', 'error_message' => $errorNote]);
            $this->info("  Post ID {$post->id} marked as POSTED." . ($errorNote ? " (partial errors)" : ''));
        } elseif ($totalPlatforms === 0) {
            // No platform was selected
            $post->update(['status' => 'failed', 'error_message' => 'Tidak ada platform dipilih']);
            $this->error("  Post ID {$post->id}: No platform selected.");
        } else {
            // All platforms failed
            $errorMsg = implode(' | ', $errors);
            $post->update(['status' => 'failed', 'error_message' => $errorMsg]);
            $this->error("  Post ID {$post->id} marked as FAILED: {$errorMsg}");
            Log::error("status:publish failed for post #{$post->id}: {$errorMsg}");
        }
    }
}
