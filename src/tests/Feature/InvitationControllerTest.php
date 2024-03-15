<?php

namespace Feature;

use App\Models\Invitation;
use App\Models\Path;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;
use Tests\Traits\CreateDataTrait;

class InvitationControllerTest extends TestCase
{
    use RefreshDatabase, CreateDataTrait;

    public function test_invitation_delete(): void
    {
        $invitation = $this->createInvitation();

        $response = $this
            ->actingAs($invitation->inviter, 'web')
            ->withHeader('X-Path-Hash', $invitation->path->hash)
            ->delete("/api/invitations/$invitation->hash");

        $response->assertStatus(200);

        $this->assertTrue(Invitation::count() === 0);
    }
}
