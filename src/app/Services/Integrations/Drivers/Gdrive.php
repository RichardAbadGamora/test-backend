<?php

namespace App\Services\Integrations\Drivers;

use App\Services\GdriveService;
use App\Traits\ValidateRrequiredFields;

class Gdrive implements DriverInterface
{
    use ValidateRrequiredFields;

    protected $gdrive;

    public function __construct()
    {
        $this->gdrive = new GdriveService();
    }

    public function updateOrCreateToken(array $data = [])
    {
        $this->validateRequiredFields($data, [
            'user_id',
            'path_id',
            'name',
            'type'
        ]);

        return $this->gdrive->updateOrCreateToken($data);
    }

    public function getData(array $data = [])
    {
        $this->validateRequiredFields($data, [
            'user_id',
            'path_id'
        ]);

        return $this->gdrive->getData($data);
    }
}
