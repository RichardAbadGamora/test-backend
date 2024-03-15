<?php

namespace Tests\Feature;

use App\Models\Path;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;
use Tests\Traits\CreateDataTrait;

class PathControllerTest extends TestCase
{
    use RefreshDatabase, CreateDataTrait;

    public function test_reorder_pin(): void
    {
        $user = $this->createUser();

        $path = $this->createPath([ 'user_id' => $user->id ]);
        $path2 = $this->createPath([ 'user_id' => $user->id ]);
        $path3 = $this->createPath([ 'user_id' => $user->id ]);

        $response = $this->actingAs($user, 'web')
            ->put("/api/paths/reorder-pin", [
                'ordering' => [
                    [
                        'path_hash' => $path->hash,
                        'order' => 1,
                    ],
                    [
                        'path_hash' => $path2->hash,
                        'order' => 2,
                    ],
                    [
                        'path_hash' => $path3->hash,
                        'order' => 3,
                    ],
                ]
            ]);

        $content = json_decode($response->getContent());

        $response->assertStatus(200);

        $this->assertEquals(1, $content->data[0]->pivot->order);
        $this->assertEquals(2, $content->data[1]->pivot->order);
        $this->assertEquals(3, $content->data[2]->pivot->order);
    }

    public function test_path_list()
    {
        $path = $this->createPath();

        $response = $this
            ->actingAs($path->user, 'web')
            ->withHeader('X-Path-Hash', $path->hash)
            ->get("/api/paths");

        $content = json_decode($response->getContent(), true);

        $response->assertStatus(200);
    }
}
