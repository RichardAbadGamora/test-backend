<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendMagicLink extends Notification
{
    use Queueable;

    protected $magicLinkUrl;

    /**
     * Create a new notification instance.
     *
     * @param string $magicLinkUrl
     */
    public function __construct($magicLinkUrl)
    {
        $this->magicLinkUrl = $magicLinkUrl;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(__('Magic Link Authentication'))
            ->line('You have received a magic link for authentication. Please click the button below to authenticate yourself.')
            ->action('Authenticate Me', $this->magicLinkUrl)
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            // You can include additional data here if needed.
        ];
    }
}
