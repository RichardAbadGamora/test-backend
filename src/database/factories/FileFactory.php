<?php

namespace Database\Factories;

use App\Enums\FileAccess;
use App\Models\Group;
use App\Models\Path;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\File>
 */
class FileFactory extends Factory
{


    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $ext = 'txt';
        $filename = $this->faker->md5 . '.' .$ext;

        return [
            'group_id' => Group::factory(),
            'page_id' => function (array $data) {
                return Group::find($data['group_id'])->page_id;
            },
            'path_id' => function (array $data) {
                return Group::find($data['group_id'])->path_id;
            },
            'user_id' => function (array $data) {
                return Group::find($data['group_id'])->user_id;
            },
            'filename' => $filename,
            'path' => $filename,
            'orig_filename' => $this->faker->word . '.' . $ext,
            'ext' => $ext,
            'disk' => 'public',
            'download_url' => "http://localhost:8000/storage/$filename",
            'access' => $this->faker->randomElement([
                FileAccess::PRIVATE,
                FileAccess::SHARED
            ])
        ];
    }
}
