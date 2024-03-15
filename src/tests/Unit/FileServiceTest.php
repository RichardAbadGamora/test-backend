<?php

namespace Tests\Unit;

use App\Models\Path;
use App\Services\ChatService;
use App\Services\FileService;
use App\Services\StorageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;
use Tests\Traits\CreateDataTrait;

class FileServiceTest extends TestCase
{
    use RefreshDatabase, CreateDataTrait;

    public function test_file_update(): void
    {
        $file = $this->createFile();

        $newName = 'foo';

        $fileService = new FileService();
        $fileService->update($file, [
            'orig_filename' => $newName
        ]);

        $this->assertEquals($newName, $file->fresh()->orig_filename);
    }
}
