<?php

namespace App\Http\Resources\Collections;

use App\Http\Resources\FolderResource;
use App\Traits\GeneratesResourceMeta;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class FolderCollection extends ResourceCollection
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
            'items' => FolderResource::collection($this->collection),
            'meta' => $this->generateMeta()
        ];
    }
}
