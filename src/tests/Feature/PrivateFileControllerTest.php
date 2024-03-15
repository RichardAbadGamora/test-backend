<?php

namespace Feature;

use App\Models\Path;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;
use Tests\Traits\CreateDataTrait;

class PrivateFileControllerTest extends TestCase
{
    use RefreshDatabase, CreateDataTrait;

    public function test_private_file_update(): void
    {
        $file = $this->createFile();

        $newName = 'foo';

        $response = $this->actingAs($file->user, 'web')
            ->withHeader('X-Path-Hash', $file->relatedPath->hash)
            ->post("/api/private-files/$file->hash", [
                'orig_filename' => $newName,
            ]);

        $content = json_decode($response->getContent());

        $response->assertStatus(200);

        $this->assertEquals($content?->data?->orig_filename, $newName);
    }
}
