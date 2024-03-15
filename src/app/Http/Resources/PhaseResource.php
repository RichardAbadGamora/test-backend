<?php

namespace App\Http\Resources;

use App\Enums\MorphKey;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PhaseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge(
            unset_keys(parent::toArray($request), ['id', 'path_id']),
            [
                'path_hash' => id_to_hash(MorphKey::PATH, $this->path_id),
                'page_hash' => id_to_hash(MorphKey::PAGE, $this->page_id),
                'items' => PhaseItemResource::collection($this->whenLoaded('items'))
            ],
        );
    }
}
