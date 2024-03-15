<?php

namespace App\Models;

use App\Services\StorageService;
use App\Traits\HasHashedId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class File extends Model implements Auditable
{
    use HasFactory, HasHashedId, \OwenIt\Auditing\Auditable, SoftDeletes;

    public static function boot()
    {
        parent::boot();

        // Delete related file at the Storage.
        static::deleting(function ($file) {
            if (!softDeletes($file)) {
                app(StorageService::class)->delete($file->path);
            }
        });
    }

    protected $appends = ['hash', 'folder_name'];

    protected $fillable = [
        'path_id',
        'page_id',
        'user_id',
        'group_id',
        'filename',
        'orig_filename',
        'ext',
        'path',
        'download_url',
        'disk',
        'access',
    ];

    public function relatedPath()
    {
        return $this->belongsTo(Path::class, 'path_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function getFolderNameAttribute()
    {
        return $this->group?->name;
    }
}
