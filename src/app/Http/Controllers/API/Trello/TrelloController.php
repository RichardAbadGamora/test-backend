<?php

namespace App\Http\Controllers\API\Trello;

use App\Enums\IntegrationType;
use App\Models\Path;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\TrelloService;
use App\Http\Controllers\Controller;
use App\Models\Integration;
use Illuminate\Support\Facades\Http;

class TrelloController extends Controller
{
    protected $trelloService;

    public function __construct(TrelloService $trelloService)
    {
        $this->trelloService = $trelloService;
    }

    public function index(User $user, Path $path)
    {
        $userId = optional($user)['id'];
        $pathId = optional($path)['id'];

        $data = Integration::where('user_id', $userId)
            ->whereType(IntegrationType::TRELLO)
            ->where('path_id', $pathId)
            ->first();

        if ($data) {
            return $data;
        } else {
            return ['message' => 'No token'];
        }
    }

    public function setAuthToken(Request $request)
    {
        return $this->trelloService->storeAccessToken($request->all());
    }

    public function setTokenMember(Request $request)
    {
        $integration = Integration::find($request->integration_id);
        $response =  Http::get($this->trelloService::TRELLO_URL . "/tokens/{$request->access_token}/member", [
            'key' => config('trello.powerup_api_key'),
            'token' => $request->access_token,
        ]);

        if ($response->successful()) {
            $responseData = $response->json();

            $integration->meta = $responseData;
            $integration->save();
        }
    }
}
