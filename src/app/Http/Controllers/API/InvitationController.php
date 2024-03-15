<?php

namespace App\Http\Controllers\API;

use App\Enums\InvitationType;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\AcceptInviteToRegAndJoinPathRequest;
use App\Http\Requests\API\GetInvitationsRequest;
use App\Http\Requests\API\ValidateInvitationRequest;
use App\Http\Resources\InvitationResource;
use App\Http\Resources\UserResource;
use App\Models\Invitation;
use App\Models\User;
use App\Services\AuthService;
use App\Services\ChatService;
use App\Traits\PaginatesOrLists;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    use PaginatesOrLists;

    public function index(GetInvitationsRequest $request)
    {
        $query = QueryBuilder::for(Invitation::class)
            ->where('path_id', request('path_id'))
            ->whereIn('type', explode(',', request('type')));

        $query = $this->paginateOrList($query);

        return $this->resolve(InvitationResource::collection($query));
    }

    public function verify(ValidateInvitationRequest $request)
    {
        $invitation = Invitation::where(request()->only('token', 'invitee_email'))
            ->with('path')
            ->firstOrFail();

        if ($invitation['type'] == InvitationType::REG_AND_JOIN_PATH) {
            $user = $this->registerAndJoinPath($invitation, $request->email);
            $invitation->user_token = $user['token'];
        }

        return $this->resolve(InvitationResource::make($invitation));
    }

    public function registerAndJoinPath(Invitation $invitation, $email)
    {
        $user = (new AuthService())->createUserByEmail($email);

        $token = $user->createToken('access-token')->plainTextToken;

        $user->paths()->syncWithPivotValues(
            $invitation->path,
            [
                'pinned' => true,
                'pinned_at' => now(),
                'role' => Role::AUTHORIZED_USER
            ]
        );

        $this->addUserToChat($invitation, $user);

        $invitation->delete();

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function joinPath(Invitation $invitation, Request $request)
    {
        $user = User::where('email', $invitation->invitee_email)->firstOrFail();

        $user->paths()->attach(
            $invitation->path,
            [
                'pinned' => true,
                'pinned_at' => now(),
                'role' => Role::AUTHORIZED_USER
            ]
        );

        $this->addUserToChat($invitation, $user);

        $invitation->delete();

        return $this->resolve(UserResource::make($user));
    }

    private function addUserToChat($invitation, $user)
    {
        app(ChatService::class)->addMember($invitation->path->hash, $user->hash);
    }

    public function destroy(Invitation $invitation)
    {
        $invitation->delete();

        return $this->resolve(null);
    }

    public function cancel(Invitation $invitation)
    {
        $invitation->delete();

        return $this->resolve(null);
    }
}
