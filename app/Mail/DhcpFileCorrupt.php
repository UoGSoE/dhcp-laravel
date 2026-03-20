<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DhcpFileCorrupt extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function envelope(): Envelope
    {
        return new Envelope(
            to: [config('dhcp.alert_email')],
            subject: config('dhcp.alert_subject_prefix').'DHCP file corrupt',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.dhcp-file-corrupt',
        );
    }
}
