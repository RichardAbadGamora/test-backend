<?php

namespace App\Models;

use App\Traits\HasHashedId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attachment extends Model
{
    use HasFactory, HasHashedId, SoftDeletes;

    protected $appends = ['hash'];

    protected $fillable = [
        'name',
        'attachable_id',
        'attachable_type',
        'filename',
        'orig_filename',
        'ext',
        'path',
        'download_url',
        'disk',
    ];

    public function attachable()
    {
        return $this->morphTo();
    }
}
