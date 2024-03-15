<?php

namespace App\Http\Controllers\API;

use App\Enums\GroupAccess;
use App\Enums\MorphKey;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\FolderRequest;
use App\Http\Resources\Collections\FolderCollection;
use App\Http\Resources\FolderResource;
use App\Models\Group;
use App\Services\FolderService;

class PrivateFolderController extends Controller
{
    public $service;

    public function __construct(FolderService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $folders = $this->service->getAll(array_merge(request()->all(), [
            'access' => GroupAccess::PRIVATE,
            'user_id' => user()->id,
        ]));

        return $this->resolve(FolderCollection::make($folders));
    }

    public function show(Group $folder)
    {
        $folder = $this->service->getOne($folder);

        return $this->resolve(FolderResource::make($folder));
    }

    public function store(FolderRequest $request)
    {
        $folder = $this->service->create(GroupAccess::PRIVATE);

        return $this->resolve(FolderResource::make($folder));
    }

    public function update(FolderRequest $request, Group $folder)
    {
        $folder = $this->service->update($folder);

        return $this->resolve(FolderResource::make($folder));
    }

    public function destroy(Group $folder)
    {
        $this->service->delete($folder);

        return $this->resolve(null);
    }

    public function restore($folder)
    {
        $folder_id = hash_to_id(MorphKey::GROUP, $folder);

        $folder = Group::withTrashed()->findOrFail($folder_id);
        $folder->restore();

        return $this->resolve(FolderResource::make($folder));
    }
}
