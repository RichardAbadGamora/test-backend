<?php

namespace App\Services\Integrations\Drivers;

use App\Traits\ValidateRrequiredFields;
use App\Services\TrelloService;
use App\Services\WaveAppsService;

class WaveApps implements DriverInterface
{
    use ValidateRrequiredFields;

    protected $waveAppsService;

    public function __construct()
    {
        $this->waveAppsService = new WaveAppsService();
    }
    public function updateOrCreateKey(array $data = [])
    {
        $this->validateRequiredFields($data, [
            'api_key',
        ]);

        return $this->waveAppsService->updateOrCreateToken($data);
    }


    public function updateOrCreateToken(array $data = [])
    {
        $this->validateRequiredFields($data, [
            'access_token',
        ]);

        return $this->waveAppsService->updateOrCreateToken($data);
    }

    public function getData(array $data = [])
    {
        $this->waveAppsService->getData($data);
    }
}
