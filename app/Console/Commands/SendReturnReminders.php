<?php

namespace App\Console\Commands;

use App\Models\BorrowTransaction;
use App\Mail\ReturnReminderMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendReturnReminders extends Command
{
    protected $signature = 'inventory:send-reminders';
    protected $description = 'Send email reminders for items that are due for return tomorrow';

    public function handle()
    {
        // Get transactions due tomorrow
        $tomorrow = now()->addDay()->toDateString();
        
        $transactions = BorrowTransaction::where('status', 'borrowed')
            ->whereDate('expected_return_date', $tomorrow)
            ->with(['requester', 'project', 'items.inventory'])
            ->get();

        $this->info("Found " . $transactions->count() . " transactions due tomorrow.");

        foreach ($transactions as $transaction) {
            $user = $transaction->requester;
            if ($user && $user->email) {
                Mail::to($user->email)->send(new ReturnReminderMail($user, $transaction));
                $this->info("Reminder sent to: " . $user->email);
            }
        }

        return Command::SUCCESS;
    }
}
