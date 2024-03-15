<?php

namespace App\Http\Middleware\API;

use App\Enums\MorphKey;
use App\Models\Path;
use App\Services\PathService;
use App\Traits\ResolvesRejects;
use Closure;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;

class ManagesPathMiddleware
{
    use ResolvesRejects;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $path = null;
        $path_param = $request->path_hash ?: $request->header('X-Path-Hash') ?: $request->path;

        if (!$path_param) {
            return $this->reject(__('messages.access-denied'), __('messages.no-permission-to-access-path'), 403);
        }

        if (is_string($path_param)) {
            $path_id = hash_to_id(MorphKey::PATH, $path_param);
            $path = Path::find($path_id);
        } else {
            $path = $path_param;
        }

        if (!app(PathService::class)->managesPath($path)) {
            return $this->reject(__('messages.access-denied'), __('messages.no-permission-to-access-path'), 403);
        }

        $request->merge(['path_id' => $path->id]);

        return $next($request);
    }
}
