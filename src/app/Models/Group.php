<?php

namespace App\Models;

use App\Traits\HasHashedId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Group extends Model implements Auditable
{
    use HasFactory, HasHashedId, \OwenIt\Auditing\Auditable, SoftDeletes;

    protected $appends = ['hash'];

    public static function boot()
    {
        parent::boot();

        self::deleting(function ($model) {
            $model->subGroups()->get()->each->delete();
            $model->files()->get()->each->delete();
        });
    }

    protected $fillable = [
        'path_id',
        'page_id',
        'user_id',
        'group_id',
        'name',
        'type',
        'access',
    ];

    public function subGroups()
    {
        return $this->hasMany(self::class, 'group_id');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'group_id');
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }
}
