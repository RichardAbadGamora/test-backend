<?php

namespace App\Http\Resources\Collections;

use App\Http\Resources\ChannelResource;
use App\Http\Resources\ChatMessageResource;
use App\Http\Resources\PathResource;
use App\Traits\GeneratesResourceMeta;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ChatMessageCollection extends ResourceCollection
{
    use GeneratesResourceMeta;

    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'items' => ChatMessageResource::collection($this->collection),
            'meta' => $this->generateMeta()
        ];
    }
}
