<?php

namespace Modules\Shop\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** 6-digit code that verifies the owner's email before a shop is created. */
class ShopOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $code, public int $minutes = 10)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: "{$this->code} is your SajiloBhoj shop verification code");
    }

    public function content(): Content
    {
        return new Content(view: 'shop::emails.otp');
    }
}
