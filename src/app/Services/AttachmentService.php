<?php

namespace App\Services;

use App\Models\Attachment;

class AttachmentService
{
    public function duplicate(Attachment $attachment, $attachable_type, $attachable_id)
    {
        $newFilename = (new StorageService())->duplicateAttachment($attachment);
        $duplicatedAttachment = Attachment::find($attachment->id)->replicate();
        $duplicatedAttachment->path = $newFilename;
        $duplicatedAttachment->filename = $newFilename;
        $duplicatedAttachment->attachable_type = $attachable_type;
        $duplicatedAttachment->attachable_id = $attachable_id;
        $duplicatedAttachment->save();

        return $duplicatedAttachment;
    }
}
