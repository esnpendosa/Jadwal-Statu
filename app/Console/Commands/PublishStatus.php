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

        $platforms = $post->platforms ?? [];
        $mediaUrl  = $post->media_url;   // Full http URL for WhatsApp
        $mediaPath = $post->media_path;  // Raw disk path for Late API upload
        $caption   = $post->caption ?? '';

        if (!$mediaPath) {
            $this->warn("Post ID {$post->id} has no media, marking as failed.");
            $post->update(['status' => 'failed', 'error_message' => 'No media path available']);
            return;
        }

        $errors = [];
        $success = true;

        // -- WhatsApp Status (uses file binary upload, works on localhost) --
        if (in_array('whatsapp_status', $platforms)) {
            $result = $whatsAppService->sendStatus($mediaPath, $caption);
            if (!$result['success']) {
                $errors[] = 'WhatsApp: ' . $result['message'];
                $success  = false;
            } else {
                $this->info("  ✓ WhatsApp Status sent.");
            }
        }

        // -- Late API (needs raw media_path for local file upload) --
        $latePlatforms = array_filter($platforms, fn($p) => in_array($p, [
            'instagram_story', 'facebook_story', 'tiktok_story'
        ]));

        if (!empty($latePlatforms)) {
            $result = $lateService->sendStory(array_values($latePlatforms), $mediaPath, $caption);
            if (!$result['success']) {
                $errors[] = 'Late API: ' . $result['message'];
                $success  = false;
            } else {
                $this->info("  ✓ Late API story posted: " . implode(', ', $latePlatforms));
            }
        }

        if ($success) {
            $post->update(['status' => 'posted', 'error_message' => null]);
            $this->info("  Post ID {$post->id} marked as POSTED.");
        } else {
            $errorMsg = implode(' | ', $errors);
            $post->update(['status' => 'failed', 'error_message' => $errorMsg]);
            $this->error("  Post ID {$post->id} marked as FAILED: {$errorMsg}");
            Log::error("status:publish failed for post #{$post->id}: {$errorMsg}");
        }
    }
}
