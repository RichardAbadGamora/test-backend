<?php

namespace App\Http\Resources;

use App\Enums\MorphKey;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FolderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge(
            unset_keys(parent::toArray($request), ['id', 'group_id', 'path_id', 'user_id']),
            [
                'group_hash' => id_to_hash(MorphKey::GROUP, $this->group_id),
                'path_hash' => id_to_hash(MorphKey::PATH, $this->path_id),
                'user_hash' => id_to_hash(MorphKey::USER, $this->user_id),
            ]
        );
    }
}
