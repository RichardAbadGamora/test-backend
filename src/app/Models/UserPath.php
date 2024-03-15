<?php

namespace App\Models;

use App\Traits\HasHashedId;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class UserPath extends Model implements Auditable
{
    use HasFactory, HasHashedId, \OwenIt\Auditing\Auditable;

    protected $appends = ['hash'];

    protected $fillable = [
        'path_id',
        'user_id',
        'pinned',
        'pinned_at',
        'role'
    ];

    public function path()
    {
        return $this->belongsTo(Path::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected function permissions(): Attribute
    {
        return Attribute::make(
            get: fn () => config("permissions.{$this->role}")
        );
    }
}
