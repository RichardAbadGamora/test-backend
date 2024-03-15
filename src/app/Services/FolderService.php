<?php

namespace App\Services;

use App\Enums\GroupType;
use App\Models\Group;
use App\Models\Path;
use App\Traits\PaginatesOrLists;
use Illuminate\Support\Arr;
use Spatie\QueryBuilder\QueryBuilder;

class FolderService
{
    use PaginatesOrLists;

    public function getAll($options = [])
    {
        $folders = [];
        $user_id = Arr::get($options, 'user_id');
        $access = Arr::get($options, 'access');
        $group_id = Arr::get($options, 'group_id');
        $path_id = Arr::get($options, 'path_id');
        $page_id = Arr::get($options, 'page_id');
        $trashed_only = Arr::get($options, 'trashed_only') === 'true';

        $path = Path::find($path_id);

        $query = $path->folders()
            ->when($trashed_only, function ($query) {
                return $query->onlyTrashed();
            }, function ($query) use ($access, $group_id, $user_id, $page_id) {
                return $query->where('page_id', $page_id)
                ->where(compact('access', 'group_id'))
                ->when($user_id, function ($query) use ($user_id) {
                    return $query->where(compact('user_id'));
                })
                ->orderBy('name');
            });

        $query = QueryBuilder::for($query);

        return $this->paginateOrList($query);
    }

    public function getOne($folder)
    {
        return $folder
            ->load('parent')
            ->load(['subGroups' => fn ($query) => $query->orderBy('name')])
            ->load(['files' => fn ($query) => $query->orderBy('orig_filename')]);
    }

    public function create($access)
    {
        return Group::create(
            array_merge(
                compact('access'),
                request()->all(),
                [
                    'type' => GroupType::FOLDER,
                    'user_id' => user()->id
                ]
            )
        );
    }

    public function update($folder)
    {
        $folder->update(request()->all());

        return $folder;
    }

    public function delete($folder)
    {
        $folder->delete();
    }
}
