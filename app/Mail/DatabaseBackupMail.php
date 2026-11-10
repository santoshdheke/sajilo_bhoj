<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class DatabaseBackupMail extends Mailable
{
    public function __construct(public string $path, public string $database) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "Database backup: {$this->database} (" . now()->format('Y-m-d H:i') . ')');
    }

    public function content(): Content
    {
        return new Content(htmlString: sprintf(
            '<p>Daily backup of the <strong>%s</strong> database is attached (%s, %s KB).</p>',
            e($this->database),
            e(basename($this->path)),
            number_format(filesize($this->path) / 1024, 1),
        ));
    }

    public function attachments(): array
    {
        return [Attachment::fromPath($this->path)->as(basename($this->path))->withMime('application/gzip')];
    }
}
