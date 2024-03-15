<?php

namespace App\Notifications;

use App\Models\Invitation;
use App\Models\Path;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class InviteToPathNotification extends Notification
{
    use Queueable;

    public $data = [];

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $inviter = User::find($this->data['inviter_id']);
        $path = Path::find($this->data['path_id']);
        $invitation = Invitation::find($this->data['invitation_id']);

        $role = __('labels.authorized-user');

        $appName = config('app.name');

        $params = ['token' => $invitation->token, 'email' => $invitation->invitee_email];
        $actionURL = url($this->data['action_url'] . '?' . http_build_query($params));

        $inviterName = $inviter->firstname ?: __('labels.someone');

        return (new MailMessage)
            ->subject(__('messages.you-are-invited-you-to-join-path-on-app-name', [
                'pathName' => $path->name,
                'appName' => $appName
            ]))
            ->greeting(__('messages.greetings-you-received-an-invite'))
            ->line(new HtmlString(__('messages.has-added-you-to-the-path-as-a', [
                'inviterName' => $inviterName,
                'pathName' => $path->name,
                'role' => $role
            ])))
            ->action(__('messages.join-your-path-now'), $actionURL)
            ->line(__('messages.thank-you'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
