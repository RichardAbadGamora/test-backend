<?php

namespace App\Services;

use App\Models\File;
use App\Models\Path;
use App\Traits\PaginatesOrLists;
use Illuminate\Support\Arr;
use Spatie\QueryBuilder\QueryBuilder;

class FileService
{
    use PaginatesOrLists;

    public $storageService = null;

    public function __construct()
    {
        $this->storageService = new StorageService();
    }
    public function getAll($options = [])
    {
        $files = [];
        $user_id = Arr::get($options, 'user_id', null);
        $access = Arr::get($options, 'access');
        $path_id = Arr::get($options, 'path_id');
        $page_id = Arr::get($options, 'page_id');
        $group_id = Arr::get($options, 'group_id', null);
        $trashed_only = Arr::get($options, 'trashed_only') === 'true';

        $path = Path::find($path_id);

        $buildQuery = $path->files()
            ->when($trashed_only, function ($query) {
                return $query->onlyTrashed();
            }, function ($query) use ($access, $group_id, $user_id, $page_id) {
                return $query->where('page_id', $page_id)
                    ->where(compact('access', 'group_id'))
                    ->when($user_id, function ($query) use ($user_id) {
                        return $query->where(compact('user_id'));
                    })
                    ->orderBy('orig_filename');
            });

        $query = QueryBuilder::for($buildQuery);

        return $this->paginateOrList($query);
    }

    public function create($access)
    {
        $file = $this->storageService->upload(request()->file('file'));

        return File::create(
            array_merge(
                $file,
                compact('access'),
                request()->all(),
                ['user_id' => user()->id]
            )
        );
    }

    public function update(File $file, array $attributes)
    {
        $file->update($attributes);

        return $file;
    }

    public function delete($file)
    {
        $file->delete();
    }
}
