<?php

namespace App\Http\Resources;

use App\Enums\MorphKey;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChannelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge(
            unset_keys(parent::toArray($request), ['id', 'path_id', 'parent_id']),
            [
                'path_hash' => id_to_hash(MorphKey::PATH, $this->path_id),
                'parent_hash' => id_to_hash(MorphKey::CHANNEL, $this->parent_id),
            ],
        );
    }
}
