<?php

namespace App\Traits;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;

trait HasHashedId
{
    public $hashOnly = true;

    public function getHashConnection()
    {
        return getMorphKey(get_called_class());
    }

    public function getRouteKey()
    {
        return Hashids::connection($this->getHashConnection())->encode($this->getKey());
    }

    public function getHashAttribute()
    {
        return $this->getRouteKey();
    }

    public function idFromHash($hash)
    {
        try {
            return Hashids::connection($this->getHashConnection())->decode($hash)[0] ?? null;
        } catch (\InvalidArgumentException $exception) {
            Log::debug("Invalid hash: $hash for {$exception->getMessage()}", [
                'user_id' => auth()?->user()?->id,
                'url' => request()->fullUrl()
            ]);
            return null;
        }
    }

    public function nonHash()
    {
        $this->hashOnly = false;

        return $this;
    }

    public function resolveId($value)
    {
        return ($this->isNumeric($value) && !$this->getHashOnly())
            ? $value
            : $this->idFromHash($value);
    }

    // NOTE: Do not delete `$field` — it will break all routes if 1 route is Not Found.
    public function resolveRouteBinding($value, $field = null)
    {
        $query = $this;
        $route = Route::current();

        $deleting = collect(optional($route)->methods())->contains('DELETE');

        if ($deleting && softDeletes($this)) {
            $query = $this->withTrashed();
        }

        $id = $this->resolveId($value);

        return $query->find($id);
    }

    protected function isNumeric($string)
    {
        return preg_match('/^[0-9]*$/', $string);
    }

    protected function getHashOnly()
    {
        if (!property_exists($this, 'hashOnly')) {
            return false;
        }

        return $this->hashOnly;
    }
}
