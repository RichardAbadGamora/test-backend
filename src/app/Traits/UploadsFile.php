<?php

namespace App\Traits;

use App\Models\Attachment;
use App\Services\StorageService;

trait UploadsFile
{
    public function uploadFile($file, $attachable_type, $attachable_id, $config = [])
    {
        $storage = new StorageService();
        $attachment = $storage->upload($file, $config);
        $name = optional($config)['name'] ?: 'file';

        $attachment = Attachment::updateOrCreate(
            compact('name', 'attachable_type', 'attachable_id'),
            array_merge(
                compact('name', 'attachable_type', 'attachable_id'),
                $attachment
            )
        );

        return $attachment;
    }
}
