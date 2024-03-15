<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Services\PathService;
use Google\Client;
use Google\Service\Gmail;

use App\Services\GmailService;
use App\Enums\IntegrationType;
use App\Services\Integrations\IntegrationFactory;
use Illuminate\Support\Facades\Auth;
use App\Models\Integration;
use App\Models\User;
use App\Models\Page;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Resources\Collections\GmailCollection;
use App\Http\Resources\GmailResource;
use App\Services\WaveAppsService;
use App\Http\Resources\WaveAppsPNLResource;

class WaveAppsController extends Controller
{
    protected $waveAppsService;

    public function __construct()
    {
        $this->waveAppsService = new WaveAppsService();
    }

    public function authenticate(Request $request)
    {
        $integration = $this->waveAppsService->authenticate($request->all());
        return $this->resolve($integration);
    }

    public function generateProfitAndLossReport(Integration $integration, Request $request)
    {
        $accountsSummary = $this->waveAppsService->generateProfitAndLossReport($integration);

        return $this->resolve(WaveAppsPNLResource::make($accountsSummary));
    }
}
