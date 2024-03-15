<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\Path;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;
use Tests\Traits\CreateDataTrait;

class ChannelControllerTest extends TestCase
{
    use RefreshDatabase, CreateDataTrait;

    public function test_channel_list(): void
    {
        $channel = $this->createChannel();

        $channel2 = $this->createChannel();

        $path = $channel->path;
        $user = $path->user;

        $response = $this
            ->actingAs($path->user, 'web')
            ->withHeader('X-Path-Hash', $path->hash)
            ->get("/api/channels");

        $content = json_decode($response->getContent());

        $response->assertStatus(200);

        $this->assertEquals(1, count($content->data->items));
    }

//    public function test_channel_store(): void
//    {
//        $path = $this->createPath();
//
//        $channel = $this->createChannel([
//            'path_id' => $path->id,
//        ]);
//
//        $response = $this
//            ->actingAs($path->user, 'web')
//            ->withHeader('X-Path-Hash', $path->hash)
//            ->post("/api/channels/sub-channel", [
//                'name' => 'new sub channel',
//                'channel_hash' => $channel->hash,
//            ]);
//
//        $content = json_decode($response->getContent());
//
//        $response->assertStatus(200);
//
//        $this->assertNotNull($content->data->name);
//    }
}
