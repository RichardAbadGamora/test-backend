<?php

namespace App\Models;

use App\Traits\HasHashedId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Invitation extends Model implements Auditable
{
    use HasFactory, HasHashedId, \OwenIt\Auditing\Auditable;

    protected $appends = ['hash'];

    protected $fillable = [
        'type',
        'token',
        'inviter_id',
        'path_id',
        'channel',
        'invitee_email',
        'invitee_contact_no',
        'expires_at',
    ];

    public function path()
    {
        return $this->belongsTo(Path::class);
    }

    public function inviter()
    {
        return $this->belongsTo(User::class);
    }
}
