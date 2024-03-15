<?php

namespace App\Services;

use App\Enums\MorphKey;
use App\Enums\PathBackgroundType;
use App\Enums\Role;
use App\Models\Page;
use App\Models\Path;
use App\Models\User;

class UserService
{
    public function createInitalPath($user)
    {
        $businessName = $user->business_name ?: hash_to_id('main', now()->timestamp);

        $path = $user->paths()->create([
            'name' => 'Your Path',
            'business_name' => $businessName,
            'icon' => null,
            'user_id' => $user->id,
        ]);

        $user->paths()->syncWithPivotValues(
            $path,
            [
                'pinned' => true,
                'pinned_at' => now(),
                'role' => Role::PATH_CREATOR,
            ]
        );
    }

    public function copyUserPathBackground(User $user, Path $path)
    {
        $params = [];

        if ($user->path_bg_type === PathBackgroundType::COLOR) {
            $params['bg_value'] = $user->path_bg_value;
            $params['bg_type'] = PathBackgroundType::COLOR;
        }

        if ($user->path_bg_type === PathBackgroundType::IMAGE) {
            $attachment = $user->pathBgImage()->first();
            (new AttachmentService())->duplicate($attachment, MorphKey::PATH, $path->id);

            $params['bg_value'] = null;
            $params['bg_type'] = PathBackgroundType::IMAGE;
        }

        $path->update($params);
    }

    public function copyUserPageBackground(User $user, Page $page)
    {
        if ($user?->page_bg_color) {
            $page->update([
                'bg_color' => $user->page_bg_color,
            ]);
        }
    }
}
