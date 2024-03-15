<?php

namespace App\Models;

use App\Traits\HasHashedId;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Models\Audit;

class Phase extends Model implements Auditable
{
    use HasFactory, HasHashedId, SoftDeletes, CascadeSoftDeletes, \OwenIt\Auditing\Auditable;

    protected $appends = ['hash'];

    protected $cascadeDeletes = ['items'];

    public $cascadeRestores = ['items'];

    protected $fillable = [
        'name',
        'path_id',
        'order',
        'page_id'
    ];

    public function path()
    {
        return $this->belongsTo(Path::class);
    }

    public function items()
    {
        return $this->hasMany(PhaseItem::class);
    }
}
