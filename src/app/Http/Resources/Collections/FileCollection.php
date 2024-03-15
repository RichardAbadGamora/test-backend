<?php

namespace App\Http\Resources\Collections;

use App\Http\Resources\FileResource;
use App\Traits\GeneratesResourceMeta;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class FileCollection extends ResourceCollection
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
            'items' => FileResource::collection($this->collection),
            'meta' => $this->generateMeta()
        ];
    }
}
