<?php

namespace App\Traits;

trait PaginatesOrLists
{
    public function paginateOrList($query, $limit = null)
    {
        $limit = $limit ?: request('limit');
        $perPage = $limit ?: 25;

        return request('all')
            ? $query->when($limit, function ($query) use ($limit) {
                $query->limit($limit);
            })->get()
            : $query->paginate($perPage);
    }
}
