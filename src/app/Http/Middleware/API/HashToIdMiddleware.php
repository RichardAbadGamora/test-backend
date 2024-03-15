<?php

namespace App\Http\Middleware\API;

use Closure;
use Error;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HashToIdMiddleware
{

    private $suffix = '_hash';
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $params = request()->all();
        $idsFromHash = [];

        foreach ($params as $key => $hash) {
            if ($this->paramEndsWithHash($key)) {
                $morphKey = str_replace($this->suffix, '', $key);
                $idsFromHash["{$morphKey}_id"] = $this->getIdFromHash($morphKey, $hash);
            }
        }

        $request->merge($idsFromHash);

        return $next($request);
    }

    public function getIdFromHash($morphKey, $hash)
    {
        switch ($morphKey) {
            case 'inviter':
                $morphKey = 'user';
                break;

            default:
                break;
        }

        if (!in_array($morphKey, getMorphKeys())) {
            throw new Error("Unknown Morph Key : $morphKey");
        }

        return hash_to_id($morphKey, $hash);
    }

    public function paramEndsWithHash($key)
    {
        return substr($key, -5) === $this->suffix;
    }
}
