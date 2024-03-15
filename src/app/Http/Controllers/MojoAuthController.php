<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use App\Services\MojoAuthService;
use Illuminate\Http\Request;

class MojoAuthController extends Controller
{
    protected $mojoAuthService;

    protected $authService;

    public function __construct()
    {
        $this->mojoAuthService = new MojoAuthService();
        $this->authService = new AuthService();
    }

    public function validateMagicLink(Request $request)
    {
        $response = $this->mojoAuthService->validateMagicLink($request->state_id);

        if ($response['status'] === 200) {
            return redirect(
                config('app.mojo_auth_magic_link_fe_redirect_url')
                . '?state_id=' . $request->state_id
            );
        }

        return $this->reject($response, __('auth.magic_link_validation_failed'));
    }
}
