<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Models\Audit;

class Activity extends Audit
{
    use HasFactory;

    public static function boot()
    {
        parent::boot();

        self::creating(function ($activity) {
            $activity->path_id = request('path_id');
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
