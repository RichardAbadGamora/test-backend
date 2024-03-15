<?php

use App\Enums\MorphKey;
use Illuminate\Database\Eloquent\Relations\Relation;

if (!function_exists('getMorphKey')) {
    function getMorphKey($class)
    {
        $morphMap = array_flip(Relation::morphMap());
        return optional($morphMap)[$class];
    }
}

if (!function_exists('getMorphedClass')) {
    function getMorphedClass($key)
    {
        return Relation::getMorphedModel($key);
    }
}

if (!function_exists('getMorphedModel')) {
    function getMorphedModel($key, $id)
    {
        $morphedClass = getMorphedClass($key);
        $model = new $morphedClass();

        return $model->find($id);
    }
}

if (!function_exists('getMorphKeys')) {
    function getMorphKeys()
    {
        return flatten_enum(MorphKey::class);
    }
}
