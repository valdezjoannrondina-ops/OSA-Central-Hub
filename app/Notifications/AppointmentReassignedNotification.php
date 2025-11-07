<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Appointment;

class AppointmentReassignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Appointment Reassigned')
            ->line('An appointment previously assigned to you has been reassigned to another staff member.')
            ->line('You no longer have access to this appointment.')
            ->line('Appointment Details:')
            ->line('Requester: ' . ($this->appointment->user ? $this->appointment->user->name : $this->appointment->full_name))
            ->line('Date: ' . $this->appointment->appointment_date)
            ->line('Message: ' . $this->appointment->message);
    }

    public function toArray($notifiable)
    {
        return [
            'appointment_id' => $this->appointment->id,
            'message' => 'Appointment reassigned. You no longer have access.'
        ];
    }
}
