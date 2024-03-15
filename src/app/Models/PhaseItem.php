<?php

namespace App\Models;

use App\Enums\MorphKey;
use App\Services\StorageService;
use App\Traits\Attachable;
use App\Traits\HasHashedId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class PhaseItem extends Model implements Auditable
{
    use HasFactory, Attachable, HasHashedId, SoftDeletes, CascadeSoftDeletes, \OwenIt\Auditing\Auditable;

    protected $cascadeDeletes = ['image'];

    public $cascadeRestores = ['image'];

    protected $auditExclude = [
        'meta'
    ];

    protected $fillable = [
        'name',
        'content_type',
        'content_value',
        'phase_id',
        'meta'
    ];

    protected $appends = ['hash'];

    protected $casts = [
        'meta' => 'array'
    ];

    public static function boot()
    {
        parent::boot();

        self::forceDeleting(function ($phaseItem) {
            $attachments = Attachment::withTrashed()->where('attachable_type', MorphKey::PHASE_ITEM)
                ->where('attachable_id', $phaseItem->id)
                ->get();

            foreach ($attachments as $key => $attachment) {
                (new StorageService())->delete($attachment->path);
                $attachment->forceDelete();
            }
        });
    }

    public function phase()
    {
        return $this->belongsTo(Phase::class);
    }

    public function image()
    {
        return $this->morphOne(Attachment::class, 'attachable')->where('name', 'file');
    }
}
