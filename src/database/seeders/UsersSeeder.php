<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'email' => fake()->email(),
            'password' => bcrypt('password123'),
            'business_name' => null
        ]);

        $this->createInitalPath($user);
    }

    private static function createInitalPath($user)
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
}
