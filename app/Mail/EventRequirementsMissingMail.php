<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Event;
use Illuminate\Support\Collection;

class EventRequirementsMissingMail extends Mailable
{
    use Queueable, SerializesModels;

    public $event;
    public $missingRequirements;

    /**
     * Create a new message instance.
     */
    public function __construct(Event $event, Collection $missingRequirements = null)
    {
        $this->event = $event;
        $this->missingRequirements = $missingRequirements ?? collect();
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Missing Requirements for Event: ' . $this->event->name)
            ->view('emails.event-requirements-missing');
    }
}
