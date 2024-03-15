<?php

namespace App\Models;

use App\Services\ChatService;
use App\Traits\HasHashedId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use OwenIt\Auditing\Contracts\Auditable;

class User extends Authenticatable implements Auditable
{
    use HasApiTokens, HasFactory, Notifiable, HasHashedId, \OwenIt\Auditing\Auditable;

    public static function boot()
    {
        parent::boot();

        self::creating(function ($user) {
            $user->business_name = $user->business_name ?: id_to_hash('main', now()->timestamp);
        });

        self::created(function ($user) {
            $user->stream_token = app(ChatService::class)->createToken($user->hash);
            $user->save();
        });
    }

    protected $appends = ['hash', 'fullname', 'initials'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'business_name',
        'email',
        'contact_no',
        'password',
        'stream_token',
        'pages_per_row',
        'path_bg_value',
        'path_bg_type',
        'gap_value',
        'margin_value',
        'page_bg_color'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'contact_no_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function paths()
    {
        return $this->belongsToMany(Path::class, 'user_paths', 'user_id', 'path_id')
            ->withPivot([
                'pinned',
                'pinned_at',
                'role',
                'order'
            ]);
    }

    protected function getFullnameAttribute()
    {
        $names = [
            $this->firstname,
            $this->lastname
        ];

        $names = array_filter($names, function ($name) {
            return !!$name;
        });

        $fullname = implode(' ', $names);

        return $fullname;
    }

    public function getInitialsAttribute()
    {
        $initials = collect([$this->firstname, $this->lastname])
            ->filter()
            ->map(function ($name) {
                return strtoupper(substr($name, 0, 1));
            })
            ->implode('');

        return $initials;
    }

    public function integrations()
    {
        return $this->hasMany(Integration::class);
    }

    public function pathBgImage()
    {
        return $this->morphOne(Attachment::class, 'attachable')->where('name', 'file');
    }

    public function providers()
    {
        return $this->hasMany(Provider::class, 'user_id', 'id');
    }
}
