<?php

namespace App\Services\Integrations\Drivers;

use App\Services\GmailService;
use App\Traits\ValidateRrequiredFields;

class Gmail implements DriverInterface
{
    use ValidateRrequiredFields;

    protected $gmailService;

    public function __construct()
    {
        $this->gmailService = new GmailService();
    }

    public function updateOrCreateToken(array $data = [])
    {
        $this->validateRequiredFields($data, [
            'user_id',
            'path_id',
            'name',
            'type'
        ]);

        return $this->gmailService->updateOrCreateToken($data);
    }

    public function getData(array $data = [])
    {
        $this->validateRequiredFields($data, [
            'user_id',
            'path_id'
        ]);

        return $this->gmailService->getData($data);
    }

    public function getThreads(array $data = [])
    {
        $this->validateRequiredFields($data, [
            'user_id',
            'path_id'
        ]);

        return $this->gmailService->getThreads($data);
    }
}
