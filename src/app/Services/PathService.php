<?php

namespace App\Services;

use App\Enums\InvitationChannel;
use App\Enums\InvitationType;
use App\Enums\PageType;
use App\Enums\PathBackgroundColor;
use App\Jobs\InviteToPathJob;
use App\Models\Invitation;
use App\Models\Page;
use App\Models\Path;
use App\Models\Phase;
use App\Models\User;
use Illuminate\Support\Arr;

class PathService
{
    public function bootPath(Path $path)
    {
        $streamChannel = app(ChatService::class)->messagingChannel($path);
        $streamChannel->create($path->user->hash);

        $this->seedDefaultPages($path);
        $this->setBackgroundColor($path, $path->user->path_bg_value);
        $this->createDefaultPhases($path->id);
        app(UserService::class)->copyUserPathBackground($path->user, $path);
    }

    public function createDefaultPhases($path_id)
    {
        $page_id = Page::where('type', PageType::OVERVIEW)
            ->where('path_id', $path_id)
            ->first()->id;

        for ($order = 1; $order <= 3; $order++) {
            $phase = Phase::create(
                array_merge(
                    ['name' => "Phase $order"],
                    compact('path_id', 'order', 'page_id')
                )
            );
        }
    }

    public function managesPath(Path $path)
    {
        return user()->paths()->find($path->id);
    }

    public function inviteUser($data)
    {
        $email = Arr::get($data, 'email');
        $user_id = Arr::get($data, 'user_id');
        $path_id = Arr::get($data, 'path_id');
        $inviter_id = Arr::get($data, 'inviter_id');

        if ($user_id) {
            $email = User::find($user_id)->email;
        }

        $fields = [
            'invitee_email' => $email,
            'path_id' => $path_id,
            'type' => $user_id ? InvitationType::JOIN_PATH : InvitationType::REG_AND_JOIN_PATH,
        ];

        $action_url = $user_id ? config('app.join_path_url') : config('app.reg_and_join_path_url');

        $invitation = Invitation::updateOrCreate(
            $fields,
            array_merge(
                [
                    'token' => md5(now()),
                    'inviter_id' => $inviter_id,
                    'channel' => InvitationChannel::MAIL,
                ],
                $fields
            )
        );

        InviteToPathJob::dispatch($email, $user_id, [
            'inviter_id' => $inviter_id,
            'path_id' => $path_id,
            'invitation_id' => $invitation->id,
            'action_url' => $action_url,
        ]);
    }

    public function setBackgroundColor(Path $path, $color = null)
    {
        $path->bg_value = $color ?: PathBackgroundColor::DEFAULT;
        $path->save();
    }

    public function seedDefaultPages($path)
    {
        $defaultPages = config('page.defaults');

        foreach ($defaultPages as $page) {
            $path->pages()->create(array_merge(
                $page,
                [
                    'user_id' => $path->user_id,
                    'path_id' => $path->id
                ]
            ));
        }
    }
}
