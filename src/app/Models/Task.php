<?php

namespace App\Models;

use App\Traits\HasHashedId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Task extends Model implements Auditable
{
    use HasFactory, HasHashedId, \OwenIt\Auditing\Auditable;

    protected $appends = ['hash'];

    protected $fillable = [
        'path_id',
        'page_id',
        'user_id',
        'task_id',
        'name',
        'type',
        'access',
        'status',
        'order',
    ];

    public function path()
    {
        return $this->belongsTo(Path::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subTasks()
    {
        return $this->hasMany(self::class, 'task_id')->orderBy('order');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'task_id');
    }
}
