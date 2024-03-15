<?php

namespace App\Models;

use App\Services\UserService;
use App\Traits\HasHashedId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory, HasHashedId;

    protected $appends = ['hash'];

    protected $casts = [
        'meta' => 'array'
    ];

    protected $fillable = [
        'name',
        'type',
        'access',
        'deletable',
        'singleton',
        'user_id',
        'path_id',
        'grid_x',
        'grid_y',
        'grid_width',
        'grid_height',
        'float_top',
        'float_left',
        'float_z_index',
        'float_transform',
        'bg_color',
        'meta'
    ];

    public static function boot()
    {
        parent::boot();

        self::created(function ($page) {
            app(UserService::class)->copyUserPageBackground($page->user, $page);
        });
    }

    public function path()
    {
        return $this->belongsTo(Path::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function integration()
    {
        return $this->hasOne(Integration::class);
    }

    public function iframe()
    {
        return $this->hasOne(Iframe::class);
    }

    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class);
    }
}
