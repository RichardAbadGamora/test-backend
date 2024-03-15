<?php

namespace Database\Factories;

use App\Enums\Access;
use App\Enums\MorphKey;
use App\Enums\PageType;
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
class PageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'path_id' => Path::factory(),
            'user_id' => function (array $data) {
                return Path::find($data['path_id'])->user_id;
            },
            'name' => $this->faker->name,
            'type' => $this->faker->randomElement(flatten_enum(PageType::class)),
            'access' => $this->faker->randomElement(flatten_enum(Access::class)),
        ];
    }

}
