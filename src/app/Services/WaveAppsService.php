<?php

namespace App\Services;

use App\Enums\IntegrationType;
use App\Models\Iframe;
use App\Models\Integration;
use App\Services\Integrations\IntegrationFactory;
use App\Models\User;
use App\Models\Page;
use Illuminate\Support\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Http;

class WaveAppsService
{
    const WAVEAPP_GGL_URI = 'https://gql.waveapps.com/graphql/public';
    const WAVEAPP_AUTH_URI = 'https://api.waveapps.com/oauth2/token/';
    private $clientId;
    private $clientSecretKey;


    public function __construct()
    {
        $this->clientId = config('services.waveapps.client_id');
        $this->clientSecretKey = config('services.waveapps.client_secret');
    }

    public function updateOrCreateToken(array $data = [])
    {
        return Integration::updateOrCreate([
            'user_id' => $data['user_id'],
            'path_id' => $data['path_id'],
            'name' => $data['name'],
            'type' => $data['type'],
        ], $data);
    }

    public function getData(array $data = [])
    {
        return Integration::where('user_id', $data['user_id'])
            ->where('path_id', $data['path_id'])
            ->where('type', IntegrationType::GMAIL)
            ->first();
    }

    public function refreshToken(Integration $integration)
    {
        $response = Http::asForm()->accept('application/json')
            ->post(self::WAVEAPP_AUTH_URI, [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecretKey,
                'refresh_token' => $integration->refresh_token,
                'grant_type' => 'refresh_token',
                'redirect_uri' => config('app.web_app_url').'/waveapps/authenticate'
                ])->object();
        if (collect($response)->has('error')) {
            throw new \Exception($response->error);
        }
        $integration->update([
            'access_token' => $response->access_token,
            'expires_at' => Carbon::now()->addSeconds($response->expires_in)
        ]);
    }

    public function authenticate($request)
    {
        $stateParams = json_decode(base64_decode($request['state']), true);
        $page = Page::find((new Page())->idFromHash($stateParams['page']));
        $response = Http::asForm()->accept('application/json')
            ->post(self::WAVEAPP_AUTH_URI, [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecretKey,
                'code' => $request['code'],
                'grant_type' => 'authorization_code',
                'redirect_uri' => config('app.web_app_url').'/waveapps/authenticate'
            ])->object();
        if (collect($response)->has('error')) {
            throw new \Exception($response->error);
        }
        $waveAppsService = new WaveAppsService();
        return $waveAppsService->storeAccessKey([
            'name' => $page->user->firstname,
            'page_id' => $page->id,
            'type' => IntegrationType::WAVEAPPS,
            'access_token' => $response->access_token,
            'refresh_token' => $response->refresh_token,
            'expires_in' => $response->expires_in
        ]);
    }

    public function storeAccessKey(array $data)
    {
        $page = Page::find($data['page_id']);
        $user = User::find($page->user_id);

        $item['access_token'] = $data['access_token'];
        $item['refresh_token'] = $data['refresh_token']?? null;
        $item['expires_in'] = $data['expires_in']?? null;
        $item['expires_at'] = Carbon::now()->addSeconds($data['expires_in']?? 0);
        $item['user_id'] = $user->id;
        $item['path_id'] = $page->path_id;
        $item['name'] = $user->firstname . "'s Access Token";
        $item['type'] = IntegrationType::WAVEAPPS;
        $integration = IntegrationFactory::create(IntegrationType::WAVEAPPS)->updateOrCreateToken($item);

        if ($page) {
            $page->integration_id = $integration->id;
            $page->save();
        }
        return IntegrationFactory::create(IntegrationType::WAVEAPPS)->updateOrCreateToken($item);
    }

    public function makeQuery(Integration $integration, String $query, array $variables): array
    {
        if (Carbon::create($integration->expires_at)->isPast()) {
            $this->refreshToken($integration);
        }
        $response = Http::
            withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$integration->access_token
            ])
            ->post(self::WAVEAPP_GGL_URI, [
                'query' => $query,
                'variables' => $variables
            ])
            ->json();
        return $response;
    }

    public function generateProfitAndLossReport(Integration $integration)
    {
        $query = 'query ($businessId: ID!, $page: Int!, $pageSize: Int!) {
            business(id: $businessId) {
              id
              accounts(page: $page, pageSize: $pageSize, types: [ASSET, EQUITY, EXPENSE, INCOME, LIABILITY]) {
                pageInfo {
                  currentPage
                  totalPages
                  totalCount
                }
                edges {
                  node {
                    id
                    name
                    description
                    displayId
                    balance
                    currency {
                        code
                        name
                        symbol
                    }
                    type {
                      name
                      value
                    }
                    subtype {
                      name
                      value
                    }
                    normalBalanceType
                    isArchived
                  }
                }
              }
            }
          }';

        $variables = [
            'businessId' => 'QnVzaW5lc3M6NDg1ZjQxYTktYmIyYi00NmM5LTgzOTktMDU4NTMyYjg0YmNm',
            'page' => 1,
            'pageSize' => 50
        ];

        $accounts = [
            'ASSET' => [],
            'EQUITY' => [],
            'EXPENSE' => [],
            'INCOME' => [],
            'LIABILITY' => []
        ];
        do {
            $response = $this->makeQuery($integration, $query, $variables);
            $totalPages = $response['data']['business']['accounts']['pageInfo']['totalPages'];
            foreach ($response['data']['business']['accounts']['edges'] as $account) {
                $accounts[$account['node']['type']['value']][] = $account['node'];
            }
        } while ($variables['page']++ < $totalPages);

        return $accounts;
    }
}
