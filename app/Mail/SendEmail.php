<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Content;

class SendEmail extends Mailable
{
    public $task;

    public function __construct($task)
    {
        $this->task = $task;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Task Created'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.task-created'
        );
    }
}
