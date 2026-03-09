<?php

namespace App\Mail;

use App\Models\BorrowTransaction;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public BorrowTransaction $borrow,
        public EmailTemplate $template,
        public string $locale,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->parsePlaceholders(
                $this->template->getSubjectForLocale($this->locale)
            ),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reminder',
        );
    }

    public function with(): array
    {
        return [
            'borrow'   => $this->borrow,
            'template' => $this->template,
            'locale'   => $this->locale,
            'body'     => $this->parsePlaceholders(
                $this->template->getBodyForLocale($this->locale)
            ),
        ];
    }

    private function parsePlaceholders(string $text): string
    {
        $borrow = $this->borrow;
        return str_replace(
            [
                '{{user_name}}', '{{item_name}}', '{{project_name}}',
                '{{quantity}}', '{{expected_return_date}}', '{{borrow_code}}'
            ],
            [
                $borrow->requester->name,
                $borrow->inventory->name,
                $borrow->project->name,
                $borrow->quantity . ' ' . $borrow->inventory->unit,
                $borrow->expected_return_date->format('d M Y'),
                $borrow->code,
            ],
            $text
        );
    }
}
