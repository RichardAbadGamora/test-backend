<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

class SystemController extends Controller
{
    public function getConfigs()
    {
        return $this->resolve([
            'stream_api_key' => config('stream.api_key')
        ]);
    }
}
