<?php

namespace App\Jobs;

use App\Models\BorrowTransaction;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReminderMail;

class SendReminderEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public BorrowTransaction $borrow,
        public string $templateCode,
    ) {}

    public function handle(): void
    {
        $user     = $this->borrow->requester;
        $locale   = $user->preferred_language ?? 'id';
        $template = EmailTemplate::where('code', $this->templateCode)->active()->first();

        if (!$template) {
            logger()->warning("Email template not found: {$this->templateCode}");
            return;
        }

        Mail::to($user->email)->send(
            new ReminderMail($this->borrow, $template, $locale)
        );
    }

    public function failed(\Throwable $e): void
    {
        logger()->error("SendReminderEmail failed for borrow {$this->borrow->code}", [
            'error' => $e->getMessage(),
        ]);
    }
}
