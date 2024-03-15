<?php

namespace Database\Factories;

use App\Enums\FileAccess;
use App\Enums\InvitationType;
use App\Models\Group;
use App\Models\Path;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invitation>
 */
class InvitationFactory extends Factory
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
            'inviter_id' => function (array $data) {
                return Path::find($data['path_id'])->user_id;
            },
            'type' => InvitationType::REG_AND_JOIN_PATH,
            'token' => $this->faker->md5,
            'channel' => 'mail',
            'invitee_email' => $this->faker->email,
        ];
    }
}
