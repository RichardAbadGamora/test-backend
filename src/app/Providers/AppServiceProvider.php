<?php

namespace App\Providers;

use App\Enums\MorphKey;
use App\Models\Attachment;
use App\Models\File;
use App\Models\Group;
use App\Models\Invitation;
use App\Models\Phase;
use App\Models\PhaseItem;
use App\Models\Path;
use App\Models\User;
use App\Models\UserPath;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::morphMap([
            MorphKey::ATTACHMENT => Attachment::class,
            MorphKey::GROUP => Group::class,
            MorphKey::PHASE => Phase::class,
            MorphKey::PHASE_ITEM => PhaseItem::class,
            MorphKey::PATH => Path::class,
            MorphKey::FILE => File::class,
            MorphKey::USER => User::class,
            MorphKey::USER_PATH => UserPath::class,
            MorphKey::INVITATION => Invitation::class,
            MorphKey::TASK => \App\Models\Task::class,
            MorphKey::PAGE => \App\Models\Page::class,
            MorphKey::CHANNEL => \App\Models\Channel::class,
        ]);
    }
}
