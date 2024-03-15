<?php

namespace App\Models;

use App\Enums\GroupType;
use App\Enums\PageType;
use App\Services\ChatService;
use App\Services\PathService;
use App\Services\UserService;
use App\Traits\HasHashedId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Path extends Model implements Auditable
{
    use HasFactory, HasHashedId, \OwenIt\Auditing\Auditable;

    protected $appends = ['hash'];

    protected $auditEvents = [
        'updated',
        'deleted',
        'restored'
    ];

    public static function boot()
    {
        parent::boot();

        self::created(function ($path) {
            app(PathService::class)->bootPath($path);
        });
    }

    protected $fillable = [
        'name',
        'business_name',
        'icon',
        'visibility',
        'bg_type',
        'bg_value',
        'page_bg_color',
        'base_text_size',
        'typo_color',
        'user_id',
        'status'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_paths')
            ->withPivot([
                'pinned',
                'pinned_at',
                'role'
            ]);
    }

    public function phases()
    {
        return $this->hasMany(Phase::class);
    }

    public function folders()
    {
        return $this->hasMany(Group::class)->where('type', GroupType::FOLDER);
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function pages()
    {
        return $this->hasMany(Page::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bgImage()
    {
        return $this->morphOne(Attachment::class, 'attachable')->where('name', 'file');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class, 'path_id', 'id');
    }

    public function channel()
    {
        return $this->hasOne(Channel::class, 'path_id', 'id');
    }
}
