<?php

namespace App\Mail;

use App\Models\Pqrs;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PqrsResponseMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Pqrs $pqrs,
        public string $response
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Respuesta a tu solicitud: ' . $this->pqrs->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.pqrs-response',
        );
    }
}