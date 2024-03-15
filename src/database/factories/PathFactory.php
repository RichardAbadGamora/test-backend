<?php

namespace Database\Factories;

use App\Enums\MorphKey;
use App\Enums\PathStatus;
use App\Enums\Role;
use App\Models\Path;
use App\Models\User;
use App\Services\ChatService;
use Closure;
use Illuminate\Database\Eloquent\Factories\Factory;
use Mockery\MockInterface;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Path>
 */
class PathFactory extends Factory
{
    public function configure()
    {
        return $this->afterCreating(function (Path $path) {
            $path->user->paths()->attach($path->id, [
                'role' => Role::PATH_CREATOR,
            ]);
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->name,
            'business_name' => function (array $data) {
                return User::find($data['user_id'])->business_name;
            },
        ];
    }

}
