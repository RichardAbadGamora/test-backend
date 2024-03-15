<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\ChannelRequest;
use App\Http\Resources\ChannelResource;
use App\Http\Resources\Collections\ChannelCollection;
use App\Http\Resources\Collections\PathCollection;
use App\Models\Channel;
use Illuminate\Support\Arr;
use Spatie\QueryBuilder\QueryBuilder;

class ChannelController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        $buildQuery = Channel::whereHas('path', function ($q) {
            $q->whereIn('id', user()->paths->pluck('id')->toArray());
        });

        $query = QueryBuilder::for($buildQuery);

        /**
         * TODO
         *
         * Filter channel list check if they are a member
         */

        $query = $this->paginateOrList($query);

        return $this->resolve(ChannelCollection::make($query));
    }

    public function storeSubChannel(ChannelRequest $request): \Illuminate\Http\JsonResponse
    {
        $params = $request->validated();

        $channel = Channel::create(array_merge($params, [
            'parent_id' => Arr::get($params, 'channel_id'),
        ]));

        $channel->streamChannel()->create(user()->hash);

        return $this->resolve(ChannelResource::make($channel));
    }
}
