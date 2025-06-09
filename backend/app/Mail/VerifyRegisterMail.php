<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifyRegisterMail extends Mailable
{
    use Queueable, SerializesModels;

    public $code; // dùng để truyền sang view

    /**
     * Create a new message instance.
     */
    public function __construct($code)
    {
        $this->code = $code;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Mã xác minh đăng ký tài khoản',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.verify-register',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

