<?php

namespace App\Mail;

use App\Models\BorrowTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BorrowNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public BorrowTransaction $borrow,
        public string $type = 'approved', // approved | reminder | overdue | created
    ) {}

    public function envelope(): Envelope
    {
        $subjects = [
            'created'  => '[Inventory] Permintaan Peminjaman Diterima - ' . $this->borrow->code,
            'approved' => '[Inventory] Peminjaman Disetujui - ' . $this->borrow->code,
            'reminder' => '[Inventory] PENGINGAT: Batas Pengembalian Besok - ' . $this->borrow->code,
            'overdue'  => '[OVERDUE] Segera Kembalikan Barang - ' . $this->borrow->code,
        ];

        return new Envelope(
            subject: $subjects[$this->type] ?? 'Notifikasi Peminjaman - ' . $this->borrow->code,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.borrow_notification',
            with: [
                'borrow' => $this->borrow,
                'type'   => $this->type,
            ]
        );
    }
}
