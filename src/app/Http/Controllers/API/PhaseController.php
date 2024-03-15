<?php

namespace App\Http\Controllers\API;

use App\Enums\MorphKey;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\PhaseRequest;
use App\Http\Resources\Collections\PhaseCollection;
use App\Http\Resources\PhaseResource;
use App\Models\Phase;
use App\Traits\PaginatesOrLists;
use Spatie\QueryBuilder\QueryBuilder;

class PhaseController extends Controller
{
    use PaginatesOrLists;

    public function index()
    {
        $path = user()->paths()->find(request('path_id'));
        $trashed_only = request('trashed_only') === 'true';

        $buildQuery = $path->phases()
            ->when($trashed_only, function ($query) {
                return $query->onlyTrashed();
            }, function ($query) {
                return $query->where('page_id', request('page_id'));
            });

        $query = QueryBuilder::for($buildQuery)
            ->allowedIncludes(['items'])
            ->allowedSorts(['order']);

        $query = $this->paginateOrList($query);

        return $this->resolve(PhaseCollection::make($query));
    }

    public function store(PhaseRequest $request)
    {
        $maxOrder = Phase::where(request()->only('path_id'))->max('order');

        $phase = Phase::create(array_merge(
            request()->all(),
            ['order' => $maxOrder + 1]
        ));

        return $this->resolve(PhaseResource::make($phase));
    }

    public function update(Phase $phase, PhaseRequest $request)
    {
        $phase->update(request()->all());

        $phase = $phase->load('items');

        return $this->resolve(PhaseResource::make($phase));
    }

    public function destroy(Phase $phase)
    {
        $phase->delete();

        return $this->resolve(PhaseResource::make($phase));
    }

    public function restore($phase)
    {
        $phase_id = hash_to_id(MorphKey::PHASE, $phase);

        $phase = Phase::withTrashed()->findOrFail($phase_id);
        $phase->restore();

        return $this->resolve(PhaseResource::make($phase));
    }
}
