<?php

namespace Modules\Shop\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Invite for a new staff member: link to choose their own password. */
class StaffInviteMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $staffName, public string $shopName, public string $link)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: "You're invited to {$this->shopName} on SajiloBhoj");
    }

    public function content(): Content
    {
        return new Content(view: 'shop::emails.staff-invite');
    }
}
