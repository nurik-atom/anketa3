<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Candidate;

class CandidateAccountCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $candidate;
    public $password;
    public $displayEmail;

    /**
     * Create a new message instance.
     */
    public function __construct(Candidate $candidate, string $password, ?string $displayEmail = null)
    {
        $this->candidate = $candidate;
        $this->password = $password;
        $this->displayEmail = $displayEmail ?: $candidate->email;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Заполни анкету заново на новом сайте Talents Lab 🚀',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.candidate-account-created',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
