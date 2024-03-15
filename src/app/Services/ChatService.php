<?php

namespace App\Services;

use App\Enums\MorphKey;
use App\Models\Channel;
use App\Models\Path;
use GetStream\StreamChat\Client as StreamClient;

class ChatService
{
    private $client = null;

    public function __construct()
    {
        $this->client = new StreamClient(config('stream.api_key'), config('stream.secret_key'));
    }

    public function messagingChannel(Path $path)
    {
        $channel = Channel::firstOrCreate([
            'path_id' => $path->id
        ], [
            'name' => $path->name,
        ]);

        $suffix = config('stream.chat_suffix');
        $streamChannel = "$channel->stream_channel_id--$suffix";

        return $this->client->Channel('messaging', $streamChannel);
    }

    public function addMember($pathHash, $memberId)
    {
        $path = Path::where('id', hash_to_id(MorphKey::PATH, $pathHash))->firstOrFail();

        $this->messagingChannel($path)->addMembers([$memberId]);
    }

    public function createToken($userId)
    {
        return $this->client->createToken($userId);
    }

    public function streamChannel(Channel $channel)
    {
        $suffix = config('stream.chat_suffix');
        return $this->client->Channel('messaging', "$channel->stream_channel_id--$suffix");
    }
}
