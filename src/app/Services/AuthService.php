<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ResolvesRejects;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService
{
    use ResolvesRejects;

    protected $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    public function emailPasswordLogin($credentials)
    {
        $user = User::where('email', $credentials['context'])->firstOrFail();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return $this->resolve(['message' => __('auth.failed')], 404);
        }

        return $this->formatResponse($user);
    }

    public function emailPasswordRegistration($data)
    {
        $user = User::create([
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'email' => $data['context'],
            'password' => bcrypt($data['password']),
            'email_verified_at' => now(),
        ]);

        $this->userService->createInitalPath($user);

        return $this->formatResponse($user);
    }

    public function loginOrRegister($email)
    {
        $user = User::where('email', $email)->first();

        if ($user) {
            return $this->emailOnlyLogin($email);
        }

        return $this->emailOnlyRegistraion($email);
    }

    public function emailOnlyLogin($email)
    {
        $user = User::where('email', $email)->firstOrFail();

        return $this->formatResponse($user);
    }

    public function emailOnlyRegistraion($email)
    {
        $user = $this->createUserByEmail($email);

        // TODO: @richthecodes - return path here so that we don't have to do the first() anymore
        $this->userService->createInitalPath($user);

        $user_path = $user->paths()->first();

        app(ChatService::class)->addMember($user_path->hash, $user->hash);

        return $this->formatResponse($user);
    }

    public function createUserByEmail($email)
    {
        $user = User::create([
            'email' => $email,
            'password' => bcrypt(Str::random(10)),
            'email_verified_at' => now(),
        ]);

        return $user;
    }

    public function phoneOnlyLogin($phone)
    {
        $user = User::where('contact_no', $phone)->firstOrFail();

        return $this->formatResponse($user);
    }

    public function phoneOnlyRegistration($phone)
    {
        $user = User::create([
            'contact_no' => $phone,
            'password' => bcrypt(Str::random(10)),
            'email_verified_at' => now(),
        ]);

        $this->userService->createInitalPath($user);

        return $this->formatResponse($user);
    }

    public function formatResponse($user)
    {
        $token = $user->createToken('access-token')->plainTextToken;

        return [
            'user' => UserResource::make($user),
            'token' => $token,
        ];
    }

    public function createUserBySocialProvider($data)
    {
        $user = $this->createUserByEmail($data['email']);

        $user->update([
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
        ]);

        // TODO: @richthecodes - return path here so that we don't have to do the first() anymore
        $this->userService->createInitalPath($user);

        $user_path = $user->paths()->first();

        app(ChatService::class)->addMember($user_path->hash, $user->hash);

        return $this->formatResponse($user);
    }
}
