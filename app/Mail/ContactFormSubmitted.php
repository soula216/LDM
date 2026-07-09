<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ContactFormSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ContactMessage $contactMessage) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nouveau message de contact — '.$this->contactMessage->name,
            replyTo: [
                new Address($this->contactMessage->email, $this->contactMessage->name),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-form-submitted',
            with: [
                'contact' => $this->contactMessage,
                'adminUrl' => url(route('admin.contact-messages.index', absolute: false)),
            ],
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        if (! $this->contactMessage->hasAttachment()) {
            return [];
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($this->contactMessage->attachment_path)) {
            return [];
        }

        $attachment = Attachment::fromStorageDisk('public', $this->contactMessage->attachment_path)
            ->as($this->contactMessage->attachment_name ?: basename($this->contactMessage->attachment_path));

        $mimeType = $disk->mimeType($this->contactMessage->attachment_path);

        if ($mimeType) {
            $attachment = $attachment->withMime($mimeType);
        }

        return [$attachment];
    }
}
