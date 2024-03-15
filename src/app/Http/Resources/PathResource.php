<?php

namespace App\Http\Resources;

use App\Enums\MorphKey;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PathResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge(
            unset_keys(parent::toArray($request), ['id', 'user_id']),
            [
                'user_hash' => $this->whenHas(
                    'user_id',
                    id_to_hash(MorphKey::USER, $this->user_id),
                ),

                'pages' => PageResource::collection($this->whenLoaded('pages')),
                'users' => UserResource::collection($this->whenLoaded('users')),
                'bg_image' => $this->bgImage,
            ],
        );
    }
}
