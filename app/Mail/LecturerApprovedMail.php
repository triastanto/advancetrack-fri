<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LecturerApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $lecturerName;

    public function __construct($lecturerName)
    {
        $this->lecturerName = $lecturerName;
    }

    public function build()
    {
        return $this->subject('Akun Anda Telah Disetujui')
            ->view('emails.lecturer-approved');
    }
} 