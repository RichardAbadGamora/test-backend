<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge(
            unset_keys(parent::toArray($request), ['user_agent', 'ip_address', 'tags', 'url']),
            [
                'user' => UserResource::make($this->user),
                'is_yours' => $this->user_id === user()->id,
            ],
        );
    }
}
