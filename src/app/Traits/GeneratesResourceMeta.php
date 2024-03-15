<?php

namespace App\Traits;

use Illuminate\Pagination\LengthAwarePaginator;

trait GeneratesResourceMeta
{
    public function generateMeta()
    {
        return $this->when($this->resource instanceof LengthAwarePaginator, fn () => [
            'pagination' => [
                'total' => $this->total(),
                'count' => $this->count(),
                'per_page' => $this->perPage(),
                'current_page' => $this->currentPage(),
                'total_pages' => $this->lastPage(),
                'links' => [
                    'previous' => $this->previousPageUrl(),
                    'next' => $this->nextPageUrl(),
                ],
            ],
        ]);
    }
}
