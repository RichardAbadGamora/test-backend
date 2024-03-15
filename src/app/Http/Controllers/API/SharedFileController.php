<?php

namespace App\Http\Controllers\API;

use App\Enums\FileAccess;
use App\Enums\MorphKey;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\FileRequest;
use App\Http\Requests\API\FileUpdateRequest;
use App\Http\Resources\Collections\FileCollection;
use App\Http\Resources\FileResource;
use App\Models\File;
use App\Services\FileService;
use App\Services\StorageService;

class SharedFileController extends Controller
{
    public function __construct(protected FileService $fileService, protected StorageService $storageService)
    {
        // ...
    }

    public function index()
    {
        $files = $this->fileService->getAll(array_merge(request()->all(), [
            'access' => FileAccess::SHARED,
        ]));

        return $this->resolve(FileCollection::make($files));
    }

    public function store(FileRequest $request)
    {
        $file = $this->fileService->create(FileAccess::SHARED);

        return $this->resolve(FileResource::make($file));
    }

    public function update(FileUpdateRequest $request, File $file)
    {
        $file = $this->fileService->update($file, $request->validated());

        return $this->resolve(FileResource::make($file));
    }

    public function destroy(File $file)
    {
        $this->fileService->delete($file);

        return $this->resolve(null);
    }

    public function restore($file)
    {
        $file_id = hash_to_id(MorphKey::FILE, $file);

        $file = File::withTrashed()->findOrFail($file_id);
        $file->restore();

        return $this->resolve(FileResource::make($file));
    }
}
