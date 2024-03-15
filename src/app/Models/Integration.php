<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Integration extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'meta' => 'array',
    ];

    protected $fillable = [
        'path_id',
        'user_id',
        'name',
        'type',
        'api_key',
        'api_secret',
        'access_token',
        'refresh_token',
        'expires_in',
        'expires_at',
        'meta',
    ];

    public function path()
    {
        return $this->belongsTo(Path::class);
    }
}
