<?php

namespace Database\Factories;

use App\Models\Page;
use App\Models\Path;
use Closure;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Group>
 */
class GroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'page_id' => Page::factory(),
            'path_id' => function (array $data) {
                return Page::find($data['page_id'])->path_id;
            },
            'user_id' => function (array $data) {
                return Path::find($data['path_id'])->user_id;
            },
            'name' => $this->faker->name,
        ];
    }

}
