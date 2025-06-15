<?php

namespace App\Mail;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DocumentSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $document;
    public $notificationData;

    /**
     * Create a new message instance.
     */
    public function __construct(Document $document, array $notificationData)
    {
        $this->document = $document;
        $this->notificationData = $notificationData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[' . config('app.name') . '] Dokumen Baru Memerlukan Verifikasi - ' . $this->document->file_name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.document-submitted',
            with: [
                'document' => $this->document,
                'notificationData' => $this->notificationData,
                'employeeName' => $this->document->employee->user->name ?? 'Unknown',
                'employeeNip' => $this->document->employee->nip ?? 'N/A',
                'documentType' => $this->document->documentType->display_name ?? 'Unknown',
                'submissionDate' => $this->notificationData['timestamp']->format('d M Y H:i'),
                'verificationUrl' => route('verification'),
            ]
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
