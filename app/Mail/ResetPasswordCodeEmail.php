<?php

namespace App\Mail;

use App\Models\User;
use App\Models\VerificationCode;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPasswordCodeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public VerificationCode $verificationCode;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, VerificationCode $verificationCode)
    {
        $this->user = $user;
        $this->verificationCode = $verificationCode;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: "Ossos Test Project",
            to: $this->user->email,
            subject: 'Reset your account password'
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reset-password-code',
            with: [
                'code' => $this->verificationCode->code,
                'validUntil' => $this->verificationCode->valid_until->format('Y-m-d H:i:s'),
            ]
        );
    }

    /**
     * Get the attachments for the message.
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
