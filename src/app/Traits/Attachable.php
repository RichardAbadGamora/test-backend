<?php

namespace App\Traits;

use App\Models\File;

trait Attachable
{
    public function attachments()
    {
        return $this->morphMany(File::class, 'attachable');
    }
}
