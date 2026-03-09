<?php

namespace App\Console\Commands;

use App\Jobs\SendReminderEmail;
use App\Models\BorrowTransaction;
use App\Models\SystemSetting;
use Illuminate\Console\Command;

class SendOverdueReminders extends Command
{
    protected $signature   = 'reminders:send-overdue';
    protected $description = 'Send email reminders for overdue borrow transactions';

    public function handle(): void
    {
        $enabled = SystemSetting::get('reminder_enabled', true);
        if (!$enabled) {
            $this->info('Reminders are disabled via system settings.');
            return;
        }

        // H-1 Reminders
        $tomorrow = BorrowTransaction::active()
            ->whereDate('expected_return_date', now()->addDay()->toDateString())
            ->with(['requester', 'inventory', 'project'])
            ->get();

        foreach ($tomorrow as $borrow) {
            SendReminderEmail::dispatch($borrow, 'reminder_h1');
        }

        $this->info("Sent H-1 reminders: {$tomorrow->count()}");

        // Overdue Reminders
        $overdue = BorrowTransaction::overdue()
            ->with(['requester', 'inventory', 'project'])
            ->get();

        foreach ($overdue as $borrow) {
            SendReminderEmail::dispatch($borrow, 'overdue');
        }

        $this->info("Sent overdue reminders: {$overdue->count()}");
    }
}
