<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\IframeRequest;
use App\Http\Resources\Collections\IframeCollection;
use App\Http\Resources\IframeResource;
use App\Models\Iframe;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class IframeController extends Controller
{
    public function index()
    {
        $query = QueryBuilder::for(Iframe::when(request('page_id'), function ($query) {
            $query->where('page_id', request('page_id'));
        }));

        $query = $this->paginateOrList($query);

        return $this->resolve(IframeCollection::make($query));
    }

    public function store(IframeRequest $request)
    {
        $iframe = Iframe::updateOrCreate(
            ['page_id' => $request->page_id],
            ['source_url' => $request->src]
        );

        return $this->resolve(IframeResource::make($iframe));
    }
}
