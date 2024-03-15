<?php

namespace App\Http\Resources\Collections;

use App\Http\Resources\PhaseResource;
use App\Traits\GeneratesResourceMeta;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PhaseCollection extends ResourceCollection
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
            'items' => PhaseResource::collection($this->collection),
            'meta' => $this->generateMeta()
        ];
    }
}
