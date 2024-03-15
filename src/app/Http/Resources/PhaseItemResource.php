<?php

namespace App\Http\Resources;

use App\Enums\MorphKey;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Mail\Attachment;

class PhaseItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge(
            unset_keys(parent::toArray($request), ['id', 'phase_id']),
            [
                'phase_hash' => id_to_hash(MorphKey::PHASE, $this->phase_id),
                'image' => AttachmentResource::make($this->image)
            ],
        );
    }
}
