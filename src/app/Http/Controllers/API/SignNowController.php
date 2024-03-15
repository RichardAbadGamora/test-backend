<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Enums\IntegrationType;
use App\Http\Requests\API\SignNowRequest;
use App\Models\Integration;
use App\Models\Path;
use App\Models\User;
use App\Services\Integrations\IntegrationFactory;
use App\Services\SignNowService;

class SignNowController extends Controller
{
    const SIGN_NOW_URL = 'https://api-eval.signnow.com';

    protected $signNowService;

    public function __construct()
    {
        $this->signNowService = new SignNowService();
    }

    public function index(User $user, Path $path)
    {
        $user_id = optional($user)['id'];
        $path_id = optional($path)['id'];

        $integration = IntegrationFactory::create(IntegrationType::SIGN_NOW);
        $data = $integration->getData(compact('user_id', 'path_id'));

        if ($data) {
            $is_valid = $this->tokenExpired($data->expires_in);
            if ($is_valid) {
                return $data->access_token;
            } else {
                $data['message'] = 'Expired token';

                return $data;
            }
        } else {
            return ['message' => 'No Access Key'];
        }
    }

    public function storeAccessKey(Request $request)
    {
        $request = $request->all();

        return $this->signNowService->storeAccessKey($request);
    }

    public function setAuthToken(SignNowRequest $request)
    {
        $request = $request->all();

        $token_response = $this->signNowService->getAuthToken($request);

        return $this->signNowService->setAuthToken($token_response, $request['access_key']);
    }

    public function getAllDocuments($token)
    {
        return $this->signNowService->getAllDocuments($token);
    }

    public function tokenExpired($seconds)
    {
        $days = ($seconds / 3600) / 24;
        $is_valid = $days > 1;
        return $is_valid;
    }
}
