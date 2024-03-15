<?php

namespace App\Http\Resources;

use App\Enums\MorphKey;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvitationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge(
            unset_keys(parent::toArray($request), ['id', 'inviter_id', 'channel', 'path_id']),
            [
                'inviter_hash' => id_to_hash(MorphKey::USER, $this->inviter_id),
                'path_hash' => id_to_hash(MorphKey::PATH, $this->path_id),
                'path' => PathResource::make($this->whenLoaded('path')),
            ]
        );
    }
}
