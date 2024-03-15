<?php

namespace App\Services\Integrations\Drivers;

use App\Traits\ValidateRrequiredFields;
use App\Services\TrelloService;

class Trello implements DriverInterface
{
    use ValidateRrequiredFields;

    protected $trelloService;

    public function __construct()
    {
        $this->trelloService = new TrelloService();
    }
    public function updateOrCreateKey(array $data = [])
    {
        $this->validateRequiredFields($data, [
            'api_key',
        ]);

        return $this->trelloService->updateOrCreateKey($data);
    }


    public function updateOrCreateToken(array $data = [])
    {
        $this->validateRequiredFields($data, [
            'access_token',
        ]);

        return $this->trelloService->updateOrCreateToken($data);
    }

    public function getData(array $data = [])
    {
        $this->trelloService->getData($data);
    }
}
