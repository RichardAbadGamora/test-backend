<?php

namespace App\Http\Middleware;

use App\Models\Path;
use App\Traits\ResolvesRejects;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserPermissionsMiddleware
{
    use ResolvesRejects;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $permission): Response
    {
        $path = Path::find($request->path_id);

        $role = Arr::get($path->users()->find(user()->id)->pivot, 'role');
        $permissions = config("permissions.$role");

        if (in_array($permission, $permissions)) {
            return $next($request);
        }

        return $this->reject('Unauthorized', 403);
    }
}
