<?php

namespace App\Models;

use App\Services\ChatService;
use App\Traits\HasHashedId;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    use HasUuids,
        HasHashedId,
        HasFactory;

    protected $appends = ['hash'];

    protected $fillable = [
        'name',
        'stream_channel_id',
        'path_id',
        'parent_id'
    ];

    public function uniqueIds(): array
    {
        return ['stream_channel_id'];
    }

    public function path()
    {
        return $this->belongsTo(Path::class, 'path_id');
    }

    public function streamChannel()
    {
        return app(ChatService::class)->streamChannel($this);
    }

    public function addMember(User $user)
    {
        return $this->streamChannel()->addMembers([$user->hash]);
    }
}
