<?php

use App\Enums\MorphKey;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the connections below you wish to use as
    | your default connection for all work. Of course, you may use many
    | connections at once using the manager class.
    |
    */

    'default' => null,

    /*
    |--------------------------------------------------------------------------
    | Hashids Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the connections setup for your application. Example
    | configuration has been included, but you may add as many connections as
    | you would like.
    |
    */

    'connections' => [
        'main' => ['salt' => 'main', 'length' => 16],
        MorphKey::ATTACHMENT => ['salt' => MorphKey::ATTACHMENT, 'length' => 16],
        MorphKey::GROUP => ['salt' => MorphKey::GROUP, 'length' => 16],
        MorphKey::PHASE => ['salt' => MorphKey::PHASE, 'length' => 16],
        MorphKey::PHASE_ITEM => ['salt' => MorphKey::PHASE_ITEM, 'length' => 16],
        MorphKey::PATH => ['salt' => MorphKey::PATH, 'length' => 16],
        MorphKey::FILE => ['salt' => MorphKey::FILE, 'length' => 16],
        MorphKey::USER => ['salt' => MorphKey::USER, 'length' => 16],
        MorphKey::USER_PATH => ['salt' => MorphKey::USER_PATH, 'length' => 16],
        MorphKey::INVITATION => ['salt' => MorphKey::INVITATION, 'length' => 16],
        MorphKey::TASK => ['salt' => MorphKey::TASK, 'length' => 16],
        MorphKey::PAGE => ['salt' => MorphKey::PAGE, 'length' => 16],
        MorphKey::CHANNEL => ['salt' => MorphKey::CHANNEL, 'length' => 16],
    ],
];
