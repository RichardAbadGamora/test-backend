<?php

namespace App\Services\Integrations;

use App\Enums\IntegrationType;
use App\Services\Integrations\Drivers\DriverInterface;
use App\Services\Integrations\Drivers\Gmail;
use App\Services\Integrations\Drivers\SignNow;
use App\Services\Integrations\Drivers\Trello;
use App\Services\Integrations\Drivers\WaveApps;
use InvalidArgumentException;

class IntegrationFactory
{
    public static function create(string $driver): DriverInterface
    {
        switch ($driver) {
            case IntegrationType::SIGN_NOW:
                return new SignNow();
            case IntegrationType::GMAIL:
                return new Gmail();
            case IntegrationType::TRELLO:
                return new Trello();
            case IntegrationType::WAVEAPPS:
                return new WaveApps();
            case IntegrationType::GDRIVE:
                return new Trello();
            default:
                throw new InvalidArgumentException("Unsupported driver: $driver");
        }
    }
}
