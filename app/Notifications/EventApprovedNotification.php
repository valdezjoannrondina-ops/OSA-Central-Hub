<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\Models\Event;

class EventApprovedNotification extends Notification
{
    use Queueable;

    protected $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $eventDate = $this->event->event_date;
        if (is_string($eventDate)) {
            try {
                $eventDate = \Carbon\Carbon::parse($eventDate);
            } catch (\Exception $e) {
                $eventDate = $this->event->event_date; // fallback to raw value
            }
        }
        return (new MailMessage)
            ->subject('Event Approved: ' . $this->event->title)
            ->greeting('Hello ' . $notifiable->first_name . ',')
            ->line('The event "' . $this->event->title . '" has been approved.')
            ->line('Date: ' . (method_exists($eventDate, 'format') ? $eventDate->format('M d, Y') : $eventDate))
            ->line('Time: ' . $this->event->start_time . ' - ' . $this->event->end_time)
            ->line('Location: ' . $this->event->location)
            ->action('View Event', url('/admin/events/' . $this->event->id))
            ->line('Thank you for using OSA Hub!');
    }

    public function toArray($notifiable)
    {
        return [
            'event_id' => $this->event->id,
            'title' => $this->event->title,
            'date' => $this->event->event_date->format('Y-m-d'),
            'start_time' => $this->event->start_time,
            'end_time' => $this->event->end_time,
            'location' => $this->event->location,
        ];
    }
}
