<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketCreated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $ticket;

    /**
     * Create a new notification instance.
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = config('app.url') . '/tickets/' . $this->ticket->id;

        return (new MailMessage)
            ->subject('New Ticket Created: #' . $this->ticket->ticket_number)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A new ticket has been created.')
            ->line('Ticket Number: ' . $this->ticket->ticket_number)
            ->line('Subject: ' . $this->ticket->subject)
            ->line('Priority: ' . $this->ticket->priority)
            ->line('Category: ' . $this->ticket->category->name)
            ->action('View Ticket', $url)
            ->line('Thank you for using our support system!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'subject' => $this->ticket->subject,
            'priority' => $this->ticket->priority,
            'category' => $this->ticket->category->name,
            'type' => 'ticket_created',
        ];
    }
}