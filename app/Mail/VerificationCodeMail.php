<?php

namespace App\Mail;

use App\Models\Setting;
use App\Models\User;
use App\Support\EmailCode;
use App\Support\EmailIcons;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerificationCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public string $code) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), $this->brandName()),
            subject: "{$this->code} is your {$this->brandName()} verification code",
        );
    }

    /** The restaurant's name (website setting, then company name); SajiloBhoj if there is none. */
    public function brandName(): string
    {
        $id   = $this->user->company_id;
        $name = $id ? Setting::getValue($id, 'restaurant_name') : null;
        $name = $name ?: $this->user->company?->name;

        return trim((string) $name) ?: 'SajiloBhoj';
    }

    public function content(): Content
    {
        // The restaurant's brand colour (picked at sign-up); every shade in the email comes from it.
        $brand = $this->user->company_id ? Setting::getValue($this->user->company_id, 'theme_color') : null;
        $brand = is_string($brand) && preg_match('/^#[0-9a-f]{6}$/i', $brand) ? strtolower($brand) : '#198754';

        return new Content(view: 'emails.verification-code', with: [
            'name'    => $this->user->name,
            'brand'   => $this->brandName(),
            'code'    => $this->code,
            'minutes' => EmailCode::TTL_MINUTES,
            'c' => [
                'brand'  => $brand,
                'dark'   => EmailIcons::mix($brand, .78, false), // code digits
                'name'   => EmailIcons::mix($brand, .55, false), // logo name, as on the website
                'tint'   => EmailIcons::mix($brand, .10),        // code box background
                'border' => EmailIcons::mix($brand, .45),        // code box border
                'line'   => EmailIcons::mix($brand, .20),        // dividers
                'page'   => EmailIcons::mix($brand, .07),        // outer background
            ],
            'icons' => [
                'logo'   => EmailIcons::brandMark($brand),
                'shield' => EmailIcons::shield($brand),
                'clock'  => EmailIcons::clock('#6b7280'),
                'heart'  => EmailIcons::heart($brand),
            ],
        ]);
    }
}
