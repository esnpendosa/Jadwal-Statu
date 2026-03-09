<?php

namespace App\Mail;

use App\Models\BorrowTransaction;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReturnReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $transaction;
    public $project;

    public function __construct(User $user, BorrowTransaction $transaction)
    {
        $this->user = $user;
        $this->transaction = $transaction;
        $this->project = $transaction->project;
    }

    public function build()
    {
        return $this->subject('Pengingat Pengembalian Barang - ' . $this->transaction->code)
                    ->markdown('emails.return_reminder');
    }
}
