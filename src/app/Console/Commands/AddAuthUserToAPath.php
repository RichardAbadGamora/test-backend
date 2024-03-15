<?php

namespace App\Console\Commands;

use App\Enums\Role;
use App\Models\User;
use Error;
use Illuminate\Console\Command;

class AddAuthUserToAPath extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'path:add-auth-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (config('app.env') !== 'local') {
            return throw new Error('This command is for local environment only.');
        }

        $users = User::take(2)->get();

        $pathCreator = $users[0];
        $authUser = $users[1];

        $path = $pathCreator->paths()->first();

        $pathCreator->paths()->attach([
            $path->id => [
                'user_id' => $authUser->id,
                'pinned' => 1,
                'pinned_at' => now(),
                'role' => Role::AUTHORIZED_USER,
            ]
        ]);

        ddJson($path);
    }
}
