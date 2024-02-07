<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ImportCompleteMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public array $errors,
    )
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Import Complete Mail',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.import_complete',
        );
    }
    public function attachments(): array
    {
        return [];
    }
}
