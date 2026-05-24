<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AssignmentSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $teacherName;
    public $studentName;
    public $lectureTitle;
    public $submittedAt;

    /**
     * Create a new message instance.
     */
    public function __construct($teacherName, $studentName, $lectureTitle, $submittedAt)
    {
        $this->teacherName = $teacherName;
        $this->studentName = $studentName;
        $this->lectureTitle = $lectureTitle;
        $this->submittedAt = $submittedAt;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '【AI Craft Reskilling】課題提出のお知らせ',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.assignment_submitted',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
