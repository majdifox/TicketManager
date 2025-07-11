<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $ticket;
    protected $oldStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(Ticket $ticket, string $oldStatus)
    {
        $this->ticket = $ticket;
        $this->oldStatus = $oldStatus;
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
            ->subject('Ticket Status Updated: #' . $this->ticket->ticket_number)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('The status of your ticket has been updated.')
            ->line('Ticket Number: ' . $this->ticket->ticket_number)
            ->line('Subject: ' . $this->ticket->subject)
            ->line('Previous Status: ' . $this->oldStatus)
            ->line('New Status: ' . $this->ticket->status)
            ->when($this->ticket->status === 'Resolved', function ($message) {
                return $message->line('Your ticket has been resolved. If you need further assistance, please create a new ticket.');
            })
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
            'old_status' => $this->oldStatus,
            'new_status' => $this->ticket->status,
            'type' => 'ticket_status_updated',
        ];
    }
}