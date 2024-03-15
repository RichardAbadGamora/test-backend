<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\InviteToPathNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class InviteToPathJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;

    protected $user_id;

    protected $data;

    /**
     * Create a new job instance.
     */
    public function __construct($email, $user_id, array $data)
    {
        $this->email = $email;
        $this->user_id = $user_id;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $invitee = new User();
        $invitee->email = $this->email;

        if ($this->user_id) {
            $invitee = User::find($this->user_id);
        }

        $invitee->notify(new InviteToPathNotification([
            'inviter_id' => $this->data['inviter_id'],
            'path_id' => $this->data['path_id'],
            'invitation_id' => $this->data['invitation_id'],
            'action_url' => $this->data['action_url'],
        ]));
    }
}
