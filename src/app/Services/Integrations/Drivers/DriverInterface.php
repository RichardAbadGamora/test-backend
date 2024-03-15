<?php

namespace App\Services\Integrations\Drivers;

use App\Models\Integrations;

interface DriverInterface
{
    public function updateOrCreateToken(array $data = []);

    public function getData(array $data = []);

    public function validateRequiredFields(array $data, array $requiredFields);
}
