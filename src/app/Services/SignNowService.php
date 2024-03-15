<?php

namespace App\Services;

use App\Enums\IntegrationType;
use App\Models\Integration;
use Illuminate\Support\Facades\Http;
use App\Services\Integrations\IntegrationFactory;

class SignNowService
{
    const SIGN_NOW_URL = 'https://api-eval.signnow.com';

    public function headers($token)
    {
        return [
            'Authorization' => $token,
            'Content-type' => 'application/x-www-form-urlencoded',
            'Accept' => 'application/json',
            'Host'
        ];
    }

    public function updateOrCreateToken(array $data = [])
    {
        return Integration::updateOrCreate([
            'api_key' => $data['api_key'],
        ], $data);
    }

    public function getData(array $data = [])
    {
        return Integration::where('user_id', $data['user_id'])
            ->where('path_id', $data['path_id'])
            ->where('type', IntegrationType::SIGN_NOW)
            ->first();
    }

    public function storeAccessKey(array $data)
    {
        $user = user();
        $integration = IntegrationFactory::create(IntegrationType::SIGN_NOW);

        $item['api_key'] = $data['access_key'];
        $item['user_id'] = $data['user_id'];
        $item['path_id'] = $data['path_id'];
        $item['name'] = $user->firstname . "'s Key";
        $item['type'] = IntegrationType::SIGN_NOW;

        return $integration->updateOrCreateToken($item);
    }

    public function getAuthToken(array $data)
    {
        $payload = [
            'username' => $data['username'],
            'password' => $data['password'],
            'grant_type' => 'password',
            'scope' => '*',
        ];

        $token = 'Basic ' . $data['access_key'];
        $url = self::SIGN_NOW_URL . '/' . 'oauth2/token';

        return Http::withHeaders($this->headers($token))->asForm()->post($url, $payload);
    }

    public function setAuthToken($data, $access_key)
    {
        $integration = IntegrationFactory::create(IntegrationType::SIGN_NOW);

        $access_token = optional($data)['access_token'];

        if ($access_token) {
            $item['api_key'] = $access_key;
            $item['access_token'] = $data['access_token'];
            $item['expires_in'] = $data['expires_in'];
            $item['refresh_token'] = $data['refresh_token'];

            return $integration->updateOrCreateToken($item);
        }

        return $data;
    }

    public function getAllDocuments($token)
    {
        $bearer_token = 'Bearer ' . $token;
        $url = self::SIGN_NOW_URL . '/' . 'user/documentsv2';

        return Http::withHeaders($this->headers($bearer_token))->get($url);
    }
}
