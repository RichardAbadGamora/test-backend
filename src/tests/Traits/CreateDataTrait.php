<?php

namespace Tests\Traits;

use App\Models\Channel;
use App\Models\File;
use App\Models\Group;
use App\Models\Invitation;
use App\Models\Page;
use App\Models\Path;
use App\Models\User;
use App\Services\ChatService;
use App\Services\PathService;
use App\Services\UserService;
use Mockery\MockInterface;
use Mockery;

trait CreateDataTrait {

    public function mockChatService()
    {
        $this->mock(ChatService::class, function (MockInterface $mock) {
            $mock->shouldReceive('createToken')->once();
            $mock->shouldReceive('messagingChannel')->once();
        });
    }

    public function createUser($attributes = [])
    {
        $this->mock(ChatService::class, function (MockInterface $mock) {
            $mock->shouldReceive('createToken')->once();
        });

        return User::factory()->create($attributes);
    }

    public function createPath($attributes = [])
    {
        $user = $this->createUser();

        $this->partialMock(PathService::class, function (MockInterface $mock) {
            $mock->shouldReceive('bootPath')->once();
        });

        if (empty($attributes['user_id'])) {
            $attributes['user_id'] = $user->id;
        }

        return Path::factory()->create($attributes);
    }

    public function createInvitation($attributes = [])
    {
        $this->mockChatService();

        return Invitation::factory()->create($attributes);
    }

    public function createGroup($attributes = [])
    {
        $this->mockChatService();

        return Group::factory()->create($attributes);
    }

    public function createFile($attributes = [])
    {
        $this->mockChatService();

        return File::factory()->create($attributes);
    }

    public function createPage($attributes = [])
    {
        $this->mockChatService();

        return Page::factory()->create($attributes);
    }

    public function createChannel($attributes = [])
    {
        $this->mock(ChatService::class, function (MockInterface $mock) {
            $mock->shouldReceive('messagingChannel');
            $mock->shouldReceive('createToken');
        });

        return Channel::factory()->create($attributes);
    }
}
