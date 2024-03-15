<?php

namespace App\Http\Controllers\API;

use App\Enums\FileAction;
use App\Enums\MagicLinkType;
use App\Enums\MorphKey;
use App\Enums\OtpAuthAction;
use App\Enums\PathBackgroundScope;
use App\Enums\PathBackgroundType;
use App\Enums\Role;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\AuthenticateRequest;
use App\Http\Requests\API\ChangeEmailOtp;
use App\Http\Requests\API\ChangeEmailRequest;
use App\Http\Requests\API\ChangePasswordRequest;
use App\Http\Requests\API\ContainerMarginRequest;
use App\Http\Requests\API\GeneralInfo;
use App\Http\Requests\API\GeneralInfoRequest;
use App\Http\Requests\API\LoginRequest;
use App\Http\Requests\API\MagicLinkRequest;
use App\Http\Requests\API\OtpRequest;
use App\Http\Requests\API\UserPageBackgroundRequest;
use App\Http\Requests\API\PageGapRequest;
use App\Http\Requests\API\PagesGapRequest;
use App\Http\Requests\API\PagesPerRowRequest;
use App\Http\Requests\API\RegisterRequest;
use App\Http\Requests\API\ResendOtpRequest;
use App\Http\Requests\API\PathBackgroundRequest;
use App\Http\Requests\API\UpdateBG;
use App\Http\Requests\API\UpdateBgRequest;
use App\Http\Requests\API\UpdateEmailRequest;
use App\Http\Requests\API\UpdateSelfRequest;
use App\Http\Requests\API\ValidateMagicLink;
use App\Http\Requests\API\VerifyOtpRequest;
use App\Http\Resources\UserResource;
use App\Models\Attachment;
use App\Models\MagicLink;
use App\Models\Path;
use App\Models\User;
use App\Services\AttachmentService;
use App\Services\AuthService;
use App\Services\MojoAuthService;
use App\Services\StorageService;
use App\Services\UserService;
use App\Traits\UploadsFile;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\MagicLinkEmail;
use App\Models\PasswordResetToken;
use App\Notifications\ForgotPassword;
use App\Notifications\SendMagicLink;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    use UploadsFile;

    protected $mojoAuthService;

    protected $authService;

    protected $storageService;

    public function __construct()
    {
        $this->mojoAuthService = new MojoAuthService();
        $this->authService = new AuthService();
        $this->storageService = new StorageService();
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->firstOrFail();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->reject([], __('auth.failed'));
        }

        $token = $user->createToken('access-token')->plainTextToken;

        return $this->resolve([
            'user' => UserResource::make($user),
            'token' => $token,
        ]);
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create(Arr::except($request->all(), ['password_confirmation']));

        $token = $user->createToken('access-token')->plainTextToken;

        (new UserService())->createInitalPath($user);

        return $this->resolve([
            'user' => UserResource::make($user),
            'token' => $token,
        ]);
    }

    public function me()
    {
        $user = user();
        $user->load('pathBgImage');

        return $this->resolve(UserResource::make($user));
    }

    public function logout()
    {
        request()->user()->currentAccessToken()->delete();

        return $this->resolve(['status' => 'success']);
    }

    public function sendOtp(OtpRequest $request)
    {
        $type = $request->keys()[0];

        if ($request->type === OtpAuthAction::MAGIC_LINK_LOGIN) {
            $response = $this->mojoAuthService->sendOtp($request->keys()[0], $request[$type]);
        } else {
            $response = $this->mojoAuthService->sendOtp($request->keys()[0], $request[$type]);
        }

        if ($response['status'] === 200) {
            return $this->resolve($response, __('auth.otp_sent'));
        }

        return $this->reject($response, __('auth.otp_failed'));
    }

    public function authenticate(AuthenticateRequest $request)
    {
        if ($request->action === OtpAuthAction::EMAIL_PASS_LOGIN) {
            return $this->resolve($this->authService->emailPasswordLogin($request->only(['context', 'password'])));
        }

        if ($request->action === OtpAuthAction::EMAIL_PASS_REGISTRATION) {
            $mojoResponse = $this->mojoAuthService->verifyOtp($request->all());

            if ($mojoResponse['status'] === 200) {
                return $this->resolve($this->authService->emailPasswordRegistration($request->all()));
            }

            return $this->reject($mojoResponse, __('auth.otp_verification_failed'));
        }

        if ($request->action === OtpAuthAction::EMAIL_ONLY_LOGIN) {
            $mojoResponse = $this->mojoAuthService->verifyOtp($request->all());

            if ($mojoResponse['status'] === 200) {
                return $this->resolve($this->authService->emailOnlyLogin($request->context));
            }

            return $this->reject($mojoResponse, __('auth.otp_verification_failed'));
        }

        if ($request->action === OtpAuthAction::EMAIL_ONLY_REGISTRATION) {
            $mojoResponse = $this->mojoAuthService->verifyOtp($request->all());

            if ($mojoResponse['status'] === 200) {
                return $this->resolve($this->authService->emailOnlyRegistraion($request->context));
            }

            return $this->reject($mojoResponse, __('auth.otp_verification_failed'));
        }

        if ($request->action === OtpAuthAction::PHONE_ONLY_LOGIN) {
            $mojoResponse = $this->mojoAuthService->verifyOtp($request->all());

            if ($mojoResponse['status'] === 200) {
                return $this->resolve($this->authService->phoneOnlyLogin($request->context));
            }

            return $this->reject($mojoResponse, __('auth.otp_verification_failed'));
        }

        if ($request->action === OtpAuthAction::PHONE_ONLY_REGISTRATION) {
            $mojoResponse = $this->mojoAuthService->verifyOtp($request->all());

            if ($mojoResponse['status'] === 200) {
                return $this->resolve($this->authService->phoneOnlyRegistration($request->context));
            }

            return $this->reject($mojoResponse, __('auth.otp_verification_failed'));
        }
    }

    public function resendOtp(ResendOtpRequest $request)
    {
        $response = $this->mojoAuthService->resendOtp(
            $request->type,
            $request->state_id
        );

        if ($response['status'] === 200) {
            return $this->resolve($response, __('auth.otp_sent'));
        }

        return $this->reject($response, __('auth.otp_failed'));
    }

    public function sendMagicLink(MagicLinkRequest $request)
    {
        $state_id = Str::random(60);

        MagicLink::where('email', $request->email)->delete();

        $magicLink = MagicLink::create([
            'type' => $request->type,
            'email' => $request->email,
            'state_id' => $state_id,
            'expires_at' => now()->addMinutes(15)
        ]);

        if ($magicLink) {
            $magicLinkUrl = config('app.mojo_auth_magic_link_fe_redirect_url') . '?state_id=' . $state_id . '&email=' . $request->email;

            $user = new User();
            $user->email = $request->email;

            $user->notify(new SendMagicLink($magicLinkUrl));

            return $this->resolve([], __('auth.magic_link_sent'));
        }

        return $this->reject([], __('auth.magic_link_failed'));
    }

    public function validateMagicLink(ValidateMagicLink $request)
    {
        $magicLink = MagicLink::where('state_id', $request->state_id)
            ->where('email', $request->email)
            ->where('expires_at', '>', now())
            ->first();

        if (!$magicLink) {
            return $this->reject([], __('auth.magic_link_validation_failed'));
        }

        if ($magicLink->type === MagicLinkType::MAGIC_LINK_LOGIN) {
            $user = $this->authService->emailOnlyLogin($request->email);
        }

        if ($magicLink->type === MagicLinkType::MAGIC_LINK_REGISTRATION) {
            $user = $this->authService->emailOnlyRegistraion($request->email);
        }

        return $this->resolve($user);
    }

    public function redirectToProvider($provider)
    {
        $validated = $this->validateProvider($provider);

        if ($validated) {
            return $validated;
        }

        return Socialite::driver($provider)->stateless()->redirect();
    }

    public function handleProviderCallback($provider)
    {
        $validated = $this->validateProvider($provider);

        if ($validated) {
            return $validated;
        }

        $providerUser = Socialite::driver($provider)->stateless()->user();

        $user = User::where('email', $providerUser->email)->first();

        if ($user) {
            $token = $user->createToken('access-token')->plainTextToken;
        } else {
            $authService = new AuthService();
            $user = $authService->createUserBySocialProvider([
                'firstname' => Arr::get($providerUser->user, 'given_name'),
                'lastname' => Arr::get($providerUser->user, 'family_name'),
                'email' => $providerUser->email
            ]);

            $token = $user['token'];
        }

        return redirect(config('app.web_app_url') . "/login/$provider/validate?token=$token");
    }

    protected function validateProvider($provider)
    {
        if (!in_array($provider, ['google', 'facebook'])) {
            return $this->reject([], __('auth.provider_not_supported'));
        }
    }

    public function forgotPassword(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->reject([], __('auth.user_not_found'));
        }

        $token = $user->createToken('access-token')->plainTextToken;

        PasswordResetToken::updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => $token,
                'created_at' => now(),
            ]
        );

        $user->notify(new ForgotPassword($token));

        return $this->resolve([], __('auth.forgot_password_email_sent'));
    }

    public function updatePassword(ChangePasswordRequest $request)
    {
        $token = PasswordResetToken::where('token', $request->token)
            ->where('email', $request->email)
            ->first();

        if (!$token) {
            return $this->reject([], __('auth.invalid_token'));
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->reject([], __('auth.user_not_found'));
        }

        $user->password = Hash::make($request->password);
        $user->save();

        $token->where('token', $request->token)
            ->where('email', $request->email)
            ->delete();

        $token = $user->createToken('access-token')->plainTextToken;

        return $this->resolve([
            'user' => UserResource::make($user),
            'token' => $token,
        ]);
    }
}
