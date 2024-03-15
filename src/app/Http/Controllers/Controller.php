<?php

namespace App\Http\Controllers;

use App\Traits\ResolvesRejects;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use ResolvesRejects, AuthorizesRequests, ValidatesRequests;
}
