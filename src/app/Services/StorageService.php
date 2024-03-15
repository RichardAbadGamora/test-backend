<?php

namespace App\Services;

use App\Models\Attachment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StorageService
{
    private $disk;
    private $storage;
    private $baseDownloadURL;

    public function __construct(array $config = [])
    {
        $this->disk = optional($config)['disk'] ?: config('filesystems.default');
        $this->baseDownloadURL = config("filesystems.disks.{$this->disk}.url");
        $this->storage = Storage::disk($this->disk);
    }

    public function upload($file, $config = [])
    {
        $size = $file->getSize();
        $pathToReplace = optional($config)['replace'] ?: '';
        $originalFilename = optional($config)['orig_filename'] ?: $file->getClientOriginalName() ?: $file->getFilename();

        $dir = optional($config)['dir'] ?: '';

        $filename = $this->storage->putFile($dir, $file, 'public');

        if ($pathToReplace) {
            $this->storage->delete($pathToReplace);
        }

        $filename = basename($filename);

        $path = $dir ? "$dir/$filename" : $filename;

        return [
            'filename' => $filename,
            'orig_filename' => $originalFilename,
            'ext' => $this->getExtension($filename),
            'path' => $path,
            'download_url' => "$this->baseDownloadURL/$path",
            'disk' => $this->disk,
            'size' => $size,
        ];
    }

    private function getExtension($filename)
    {
        $parts = explode('.', $filename);
        return $parts[count($parts) - 1];
    }

    public function delete($files)
    {
        $this->storage->delete($files);
    }

    public function list($config = [])
    {
        $dir = optional($config)['dir'] ?: '';
        $all = optional($config)['all'] ?: false;

        return $all ? $this->storage->allFiles($dir) : $this->storage->files($dir);
    }

    public function duplicateAttachment(Attachment $attachment)
    {
        $filename = Str::random(50) . '.' . $attachment->ext;
        $this->storage->copy($attachment->path, $filename, 'public');

        return $filename;
    }
}
