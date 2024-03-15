<?php

use App\Models\User;
use Vinkla\Hashids\Facades\Hashids;
use App\Models\Integration;

if (!function_exists('integration')) {
    function integration()
    {
        return Integration::find(auth()->id());
    }
}

if (!function_exists('user')) {
    function user()
    {
        return User::find(auth()->id());
    }
}

if (!function_exists('ddJSON')) {
    function ddJSON($value)
    {
        echo json_encode($value, JSON_PRETTY_PRINT);
        dd();
    }
}

if (!function_exists('softDeletes')) {
    function softDeletes($model)
    {
        return method_exists($model, 'runSoftDelete');
    }
}

if (!function_exists('hash_to_id')) {
    function hash_to_id($morhpKey, $hash)
    {
        return $hash ? (Hashids::connection($morhpKey)->decode($hash)[0] ?? null) : null;
    }
}

if (!function_exists('id_to_hash')) {
    function id_to_hash($morhpKey, $id)
    {
        return $id ? Hashids::connection($morhpKey)->encode($id) : null;
    }
}

if (!function_exists('unset_keys')) {
    function unset_keys($array, $keys)
    {
        foreach ($keys as $key) {
            unset($array[$key]);
        }

        return $array;
    }
}

if (!function_exists('flatten_enum')) {
    function flatten_enum($enumClass)
    {
        $reflection = new ReflectionClass($enumClass);
        return array_values($reflection->getConstants());
    }
}

if (!function_exists('userCan')) {
    function userCan($user, $permission)
    {
        return $user->hasPermissionTo($permission);
    }
}
