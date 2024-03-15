<?php

namespace App\Services;

use Exception;
use App\Models\Integration;
use App\Enums\IntegrationType;
use App\Models\Path;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\ConnectionException;
use App\Services\Integrations\IntegrationFactory;

class TrelloService
{
    const TRELLO_URL = 'https://api.trello.com/1';

    protected $path;

    public function setPath(Path $path)
    {
        $this->path = $path;

        return $this;
    }

    public function request($method, string $endpoint, array $data = [])
    {
        return Http::connectTimeout(3)
            ->retry(3, 100, function (Exception $exception, PendingRequest $request) {
                return $exception instanceof ConnectionException;
            })
            ->$method($this->buildUrl($endpoint), $this->mergeParams($data));
    }

    public function get($endpoint, $queryParams = [])
    {
        return $this->request('get', $endpoint, $queryParams);
    }

    public function post($endpoint, $data = [])
    {
        return $this->request('post', $endpoint, $data);
    }

    public function put($endpoint, $data = [])
    {
        return $this->request('put', $endpoint, $data);
    }

    public function delete($endpoint)
    {
        return $this->request('delete', $endpoint);
    }

    protected function buildUrl($endpoint)
    {
        return self::TRELLO_URL . $endpoint;
    }

    protected function mergeParams($queryParams = [])
    {
        $integration = Integration::where('user_id', auth()->user()->id)
            ->whereType(IntegrationType::TRELLO)
            ->where('path_id', $this->path->id)
            ->first();

        $defaultParams = [
            'key' => config('trello.powerup_api_key'),
            'token' => $integration->access_token,
        ];

        // Replace null values with empty strings (trello won't access null as empty)
        $queryParams = array_map(function ($value) {
            return is_null($value) ? '' : $value;
        }, $queryParams);

        return array_merge($defaultParams, $queryParams);
    }


    public function updateOrCreateKey(array $data = [])
    {
        return Integration::updateOrCreate([
            'api_key' => $data['api_key'],
        ], $data);
    }


    public function updateOrCreateToken(array $data = [])
    {
        return Integration::updateOrCreate([
            'access_token' => $data['access_token'],
        ], $data);
    }

    public function getData(array $data = [])
    {
        return Integration::where('user_id', $data['user_id'])
            ->where('path_id', $data['path_id'])
            ->whereType(IntegrationType::TRELLO)
            ->first();
    }

    public function storeAccessToken(array $data)
    {
        $user = user();
        $integration = IntegrationFactory::create(IntegrationType::TRELLO);

        $item['access_token'] = $data['access_token'];
        $item['user_id'] = $data['user_id'];
        $item['path_id'] = $data['path_id'];
        $item['name'] = $user->firstname . "'s Token";
        $item['type'] = IntegrationType::TRELLO;

        return $integration->updateOrCreateToken($item);
    }

    public function tokenExpired($expires_in)
    {
        $now = time();
        $expires_at = $expires_in + $now;

        return $expires_at > $now;
    }
}
