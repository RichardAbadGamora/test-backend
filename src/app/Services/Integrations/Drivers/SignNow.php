<?php

namespace App\Services\Integrations\Drivers;

use App\Services\SignNowService;
use App\Traits\ValidateRrequiredFields;

class SignNow implements DriverInterface
{
    use ValidateRrequiredFields;

    protected $signNowService;

    public function __construct()
    {
        $this->signNowService = new SignNowService();
    }

    public function updateOrCreateToken(array $data = [])
    {
        $this->validateRequiredFields($data, [
            'api_key',
        ]);

        return $this->signNowService->updateOrCreateToken($data);
    }

    public function getData(array $data = [])
    {
        return $this->signNowService->getData($data);
    }
}
