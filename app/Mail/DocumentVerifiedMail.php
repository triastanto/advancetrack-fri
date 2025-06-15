<?php

namespace App\Mail;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DocumentVerifiedMail extends Mailable
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
            subject: '[' . config('app.name') . '] Dokumen Telah Diverifikasi - ' . $this->document->file_name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.document-verified',
            with: [
                'document' => $this->document,
                'notificationData' => $this->notificationData,
                'employeeName' => $this->document->employee->user->name ?? 'Unknown',
                'employeeNip' => $this->document->employee->nip ?? 'N/A',
                'documentType' => $this->document->documentType->display_name ?? 'Unknown',
                'verificationComment' => $this->notificationData['comment'] ?? 'Tidak ada catatan.',
                'verifyingStaff' => $this->notificationData['user_name'] ?? 'Staff',
                'approvalTimestamp' => $this->notificationData['timestamp']->format('d M Y, H:i') ?? 'N/A',
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
