<?php

namespace App\Http\Resources;

use App\Enums\MorphKey;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttachmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge(
            unset_keys(parent::toArray($request), ['id', 'attachable_id']),
            [
                'attachable_hash' => id_to_hash(MorphKey::ATTACHMENT, $this->attachable_id),
            ],
        );
    }
}
