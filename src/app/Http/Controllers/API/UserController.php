<?php

namespace App\Http\Controllers\API;

use App\Enums\FileAction;
use App\Enums\MorphKey;
use App\Enums\PathBackgroundScope;
use App\Enums\PathBackgroundType;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\ChangeEmailOtp;
use App\Http\Requests\API\ChangeEmailRequest;
use App\Http\Requests\API\ChangePasswordRequest;
use App\Http\Requests\API\ContainerMarginRequest;
use App\Http\Requests\API\GeneralInfoRequest;
use App\Http\Requests\API\PageGapRequest;
use App\Http\Requests\API\PagesPerRowRequest;
use App\Http\Requests\API\PathBackgroundRequest;
use App\Http\Requests\API\UserPageBackgroundRequest;
use App\Http\Resources\Collections\ActivityCollection;
use App\Http\Resources\Collections\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AttachmentService;
use App\Services\AuthService;
use App\Services\MojoAuthService;
use App\Services\StorageService;
use App\Traits\PaginatesOrLists;
use App\Traits\UploadsFile;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class UserController extends Controller
{
    use PaginatesOrLists;

    use UploadsFile;

    protected $mojoAuthService;

    protected $storageService;

    public function __construct()
    {
        $this->mojoAuthService = new MojoAuthService();
        $this->storageService = new StorageService();
    }

    public function index()
    {
        $query = QueryBuilder::for(User::class)
            ->allowedFilters('email', 'firstname', 'lastname')
            ->when(
                request('invitable'),
                fn ($query) => $query->whereDoesntHave(
                    'paths',
                    fn ($query) => $query
                        ->where('paths.id', request('path_id'))
                )
            );

        $query = $this->paginateOrList($query);

        return $this->resolve(UserCollection::make($query));
    }

    public function show(User $user)
    {
        return $this->resolve(UserResource::make($user));
    }

    public function update(User $user, Request $request)
    {
        $params = $request->all();

        $user->update($params);

        return $this->resolve(UserResource::make($user));
    }

    public function updateGeneralInfo(GeneralInfoRequest $request)
    {
        $user = $request->user();

        $user->update($request->only('firstname', 'lastname'));

        return $this->resolve(UserResource::make($user));
    }

    public function updatePathBackground(PathBackgroundRequest $request)
    {
        $file = null;
        $payload = request()->all();
        $user = $request->user();
        $image = $user->pathBgImage;

        $payload['path_bg_type'] = $request->bg_type;
        $payload['path_bg_value'] = $request->bg_value;

        if ($payload['path_bg_type'] === PathBackgroundType::IMAGE) {
            $file = request()->file('bg_value');
            $payload['path_bg_value'] = null;

            if ($payload['file_action'] === FileAction::CHANGE) {
                $image?->delete();

                $this->uploadFile($file, MorphKey::USER, $user->id);
            }
        } else {
            $payload['file_action'] = FileAction::DELETE;
        }

        if ($payload['file_action'] === FileAction::DELETE && $image) {
            $this->storageService->delete($image->path);
            $image->delete();
        }

        if ($request->path_bg_type === PathBackgroundType::COLOR) {
            if ($image) {
                $this?->storageService?->delete($image?->path);
                $image?->delete();
            }
        }

        $user->update($payload);
        $user_paths = $user->paths()->where('role', Role::PATH_CREATOR)->get();

        if ($payload['path_bg_type'] === PathBackgroundType::COLOR) {
            foreach ($user_paths as $key => $path) {
                $path->bg_type = PathBackgroundType::COLOR;
                $path->bg_value = $payload['path_bg_value'];
                $path->save();
            }
        }

        if ($payload['path_bg_type'] === PathBackgroundType::IMAGE) {
            foreach ($user_paths as $key => $path) {
                $image = $path->bgImage;

                // delete path current image
                if ($image) {
                    $this?->storageService?->delete($image?->path);
                    $image?->delete();
                }

                $attachment = $user->pathBgImage()->first();

                (new AttachmentService())->duplicate($attachment, MorphKey::PATH, $path->id);

                // delete attachment current image
                if ($attachment?->bgImage) {
                    $this?->storageService?->delete($attachment?->bgImage);
                }

                $path->update([
                    'bg_type' => PathBackgroundType::IMAGE,
                    'bg_value' => null
                ]);
            }
        }
        return $this->resolve(UserResource::make($user));
    }

    public function updatePageBackground(UserPageBackgroundRequest $request)
    {
        $user = $request->user();

        $user->update(request()->only('page_bg_color'));

        foreach ($user->paths()->where('role', Role::PATH_CREATOR)->get() as $key => $path) {
            $path->pages()->update([
                'bg_color' => $request->page_bg_color
            ]);

            $path->update([
                'page_bg_color' => $request->page_bg_color
            ]);
        }

        return $this->resolve(UserResource::make($user));
    }

    public function updatePagesPerRow(PagesPerRowRequest $request)
    {
        $user = $request->user();

        $user->update($request->only('pages_per_row'));

        return $this->resolve(UserResource::make($user));
    }

    public function changeEmailOtp(ChangeEmailOtp $request)
    {
        $response = $this->mojoAuthService->sendOtp('email', $request->email);

        return $this->resolve($response);
    }

    public function changeEmail(ChangeEmailRequest $request)
    {
        $user = $request->user();

        $response = $this->mojoAuthService->verifyOtp([
            'otp_type' => 'email',
            'otp_value' => $request->otp,
            'state_id' => $request->state_id
        ]);

        if ($response['status'] === 200) {
            $user->update($request->only('email'));

            return $this->resolve(UserResource::make($user));
        } else {
            return $this->responseError(['errors' => ['otp' => [__('auth.otp_verification_failed')]]]);
        }
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $user = $request->user();

        $user->update($request->only('password'));

        return $this->resolve(UserResource::make($user));
    }

    public function updatePageGaps(PageGapRequest $request)
    {
        $user = $request->user();

        $user->update($request->only('gap_value'));

        return $this->resolve(UserResource::make($user));
    }

    public function updateContainerMargins(ContainerMarginRequest $request)
    {
        $user = $request->user();

        $user->update($request->only('margin_value'));

        return $this->resolve(UserResource::make($user));
    }
}
