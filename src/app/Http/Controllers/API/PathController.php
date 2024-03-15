<?php

namespace App\Http\Controllers\API;

use App\Enums\FileAction;
use App\Enums\MorphKey;
use App\Enums\PathBackgroundType;
use App\Enums\PathStatus;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\InviteToPathRequest;
use App\Http\Requests\API\PathBackgroundRequest;
use App\Http\Requests\API\PathGeneralInfoRequest;
use App\Http\Requests\API\PathPageBackgroundRequest;
use App\Http\Requests\API\PathRequest;
use App\Http\Requests\API\ReorderPinnedPathsRequest;
use App\Http\Resources\Collections\ActivityCollection;
use App\Http\Resources\Collections\PathCollection;
use App\Http\Resources\PathResource;
use App\Http\Resources\UserResource;
use App\Models\Path;
use App\Services\AttachmentService;
use App\Services\PathService;
use App\Services\StorageService;
use App\Traits\PaginatesOrLists;
use App\Traits\UploadsFile;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Spatie\QueryBuilder\QueryBuilder;

class PathController extends Controller
{
    use PaginatesOrLists, UploadsFile;

    protected $storageService;

    public function __construct()
    {
        $this->storageService = new StorageService();
    }

    public function index()
    {
        $buildQuery = user()
            ->paths()
            ->when(
                request('pinned'),
                fn($q) => $q->where('user_paths.pinned', true)
            )
            ->orderBy('user_paths.pinned_at', 'asc');

        $query = QueryBuilder::for($buildQuery)
            ->allowedFilters(['status']);

        $query = $this->paginateOrList($query);

        return $this->resolve(PathCollection::make($query));
    }

    public function store(PathRequest $request)
    {
        $params = $request->all();

        $user = user();

        $pin = $user->paths()->where('status', request('status', PathStatus::ACTIVE))->wherePivot('pinned', true)->doesntExist();

        $path = $user->paths()->create(
            array_merge(
                $params,
                [
                    'user_id' => $user->id,
                    'business_name' => $user->business_name,
                ]
            )
        );

        $user->paths()
            ->sync(
                [
                    $path->id => [
                        'role' => Role::PATH_CREATOR,
                        'pinned' => $pin
                    ]
                ],
                false
            );

        return $this->resolve(PathResource::make($path));
    }

    public function update(Path $path, PathRequest $request)
    {
        $payload = $request->all();
        $image = $path->bgImage;

        if ($payload['bg_type'] === PathBackgroundType::IMAGE) {
            $file = request()->file('bg_value');
            $payload['bg_value'] = null;

            if ($payload['file_action'] === FileAction::CHANGE) {
                $image?->delete();

                $this->uploadFile($file, MorphKey::PATH, $path->id);
            }
        } else {
            $payload['file_action'] = FileAction::DELETE;
        }

        if ($payload['file_action'] === FileAction::DELETE && $image) {
            $this->storageService->delete($image->path);
            $image->delete();
        }

        if ($request->bg_type === PathBackgroundType::COLOR) {
            if ($image) {
                $this?->storageService?->delete($image?->path);
                $image?->delete();
            }
        }

        $path->update(array_merge($payload, [
            'business_name' => request('business_name', $path->owner->business_name)
        ]));

        return $this->resolve(PathResource::make($path));
    }

    public function show(Path $path)
    {
        $path->load('pages', 'users');

        return $this->resolve(PathResource::make($path));
    }

    public function pin(Path $path)
    {
        $pin = !!request('pin', true);

        $path
            ->users()
            ->updateExistingPivot(
                user()->id,
                [
                    'pinned' => $pin,
                    'pinned_at' => $pin ? now() : null,
                ]
            );

        return $this->resolve(PathResource::make($path));
    }

    public function inviteUser(Path $path, InviteToPathRequest $request)
    {
        app(PathService::class)->inviteUser(
            array_merge($request->all(), ['inviter_id' => user()->id])
        );

        return $this->resolve(__('invitation-sent'));
    }

    public function me(Path $path, Request $request)
    {
        $role = Arr::get($path->users()->find(user()->id)->pivot, 'role');
        $permissions = config("permissions.$role");

        return $this->resolve(
            array_merge(
                compact('role', 'permissions'),
                ['user' => UserResource::make(user())]
            )
        );
    }

    public function updatePageBackground(Path $path, PathPageBackgroundRequest $request)
    {
        $path->update($request->only('page_bg_color'));

        $path->pages()->update(['bg_color' => $request->page_bg_color]);

        return $this->resolve(PathResource::make($path));
    }

    public function updatePathBackground(Path $path, PathBackgroundRequest $request)
    {
        $payload = $request->all();
        $image = $path->bgImage;

        if ($payload['bg_type'] === PathBackgroundType::IMAGE) {
            $file = request()->file('bg_value');
            $payload['bg_value'] = null;

            if ($payload['file_action'] === FileAction::CHANGE) {
                $image?->delete();

                $this->uploadFile($file, MorphKey::PATH, $path->id);
            }
        } else {
            $payload['file_action'] = FileAction::DELETE;
        }

        if ($payload['file_action'] === FileAction::DELETE && $image) {
            $this->storageService->delete($image->path);
            $image->delete();
        }

        if ($request->bg_type === PathBackgroundType::COLOR) {
            if ($image) {
                $this?->storageService?->delete($image?->path);
                $image?->delete();
            }
        }

        $path->update($payload);

        return $this->resolve(PathResource::make($path));
    }

    public function updateGeneralInfo(Path $path, PathGeneralInfoRequest $request)
    {
        $path->update(array_merge($request->only('name'), [
            'business_name' => request('business_name', $path->owner->business_name)
        ]));

        return $this->resolve(PathResource::make($path));
    }

    public function activities(Path $path, Request $request)
    {
        $buildQuery = $path->activities();
        $query = QueryBuilder::for($buildQuery)
            ->with([
                'user',
                'auditable' => function ($q) {
                    // $q->withTrashed();
                }
            ]);

        $query = $this->paginateOrList($query);

        return $this->resolve(ActivityCollection::make($query));
    }

    public function archive(Path $path)
    {
        $path->update(['status' => PathStatus::ARCHIVED]);

        return $this->resolve(PathResource::make($path));
    }

    public function unarchive(Path $path)
    {
        $path->update(['status' => PathStatus::ACTIVE]);

        return $this->resolve(PathResource::make($path));
    }

    public function reorderPin(ReorderPinnedPathsRequest $request)
    {
        $user = $request->user();

        foreach ($request->ordering as $order) {
            $pathId = hash_to_id(MorphKey::PATH, $order['path_hash']);

            $request->user()->paths()->updateExistingPivot($pathId, [
                'order' => $order['order']
            ]);
        }

        $userPaths = $user->paths()->whereStatus(PathStatus::ACTIVE)->get();

        return $this->resolve($userPaths);
    }

    public function getUsers(Request $request, Path $path)
    {
        return $this->resolve($path->users);
    }

    public function removeAccess(Request $request, Path $path)
    {
        $path->users()->detach($request->user_id);

        return $this->resolve([], __('messages.access-revoke-success'));
    }
}
