<?php

namespace Tests\Feature;

use App\Models\Friend;
use App\Models\Jar;
use App\Models\JarInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JarInvitationTest extends TestCase
{
    use RefreshDatabase;

    private function makeFriends(User $a, User $b): void
    {
        Friend::create(['user_id' => $a->id, 'friend_id' => $b->id, 'status' => 'accepted']);
        Friend::create(['user_id' => $b->id, 'friend_id' => $a->id, 'status' => 'accepted']);
    }

    // -------------------------------------------------------------------------
    // Sending invitations
    // -------------------------------------------------------------------------

    public function test_jar_member_can_invite_a_user(): void
    {
        $inviter = User::factory()->create();
        $invitee = User::factory()->create();
        $jar     = Jar::factory()->create(['created_by' => $inviter->id]);

        $jar->users()->syncWithoutDetaching([$inviter->id => ['streak' => 0]]);

        $response = $this->actingAs($inviter)->postJson(route('jar-invitations.store'), [
            'jar_id'     => $jar->id,
            'invitee_id' => $invitee->id,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('jar_invitations', [
            'jar_id'     => $jar->id,
            'invitee_id' => $invitee->id,
            'status'     => 'pending',
        ]);
    }

    public function test_non_member_cannot_invite_a_user(): void
    {
        $nonMember = User::factory()->create();
        $invitee   = User::factory()->create();
        $jar       = Jar::factory()->create();

        $response = $this->actingAs($nonMember)->postJson(route('jar-invitations.store'), [
            'jar_id'     => $jar->id,
            'invitee_id' => $invitee->id,
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('jar_invitations', ['jar_id' => $jar->id, 'invitee_id' => $invitee->id]);
    }

    public function test_cannot_invite_user_who_is_already_in_the_jar(): void
    {
        $inviter = User::factory()->create();
        $invitee = User::factory()->create();
        $jar     = Jar::factory()->create(['created_by' => $inviter->id]);

        $jar->users()->syncWithoutDetaching([
            $inviter->id => ['streak' => 0],
            $invitee->id => ['streak' => 0],
        ]);

        $response = $this->actingAs($inviter)->postJson(route('jar-invitations.store'), [
            'jar_id'     => $jar->id,
            'invitee_id' => $invitee->id,
        ]);

        $response->assertUnprocessable();
    }

    // -------------------------------------------------------------------------
    // Accepting invitations
    // -------------------------------------------------------------------------

    public function test_invitee_can_accept_a_pending_invitation(): void
    {
        $inviter    = User::factory()->create();
        $invitee    = User::factory()->create();
        $jar        = Jar::factory()->create(['created_by' => $inviter->id]);
        $invitation = JarInvitation::create([
            'jar_id'     => $jar->id,
            'inviter_id' => $inviter->id,
            'invitee_id' => $invitee->id,
            'status'     => 'pending',
        ]);

        $response = $this->actingAs($invitee)->postJson(route('jar-invitations.accept', $invitation));

        $response->assertOk();
        $this->assertDatabaseHas('jar_invitations', ['id' => $invitation->id, 'status' => 'accepted']);
        $this->assertDatabaseHas('jar_users', ['jar_id' => $jar->id, 'user_id' => $invitee->id]);
    }

    public function test_non_invitee_cannot_accept_an_invitation(): void
    {
        $inviter    = User::factory()->create();
        $invitee    = User::factory()->create();
        $stranger   = User::factory()->create();
        $jar        = Jar::factory()->create(['created_by' => $inviter->id]);
        $invitation = JarInvitation::create([
            'jar_id'     => $jar->id,
            'inviter_id' => $inviter->id,
            'invitee_id' => $invitee->id,
            'status'     => 'pending',
        ]);

        $response = $this->actingAs($stranger)->postJson(route('jar-invitations.accept', $invitation));

        $response->assertForbidden();
    }

    public function test_expired_invitation_cannot_be_accepted(): void
    {
        $inviter    = User::factory()->create();
        $invitee    = User::factory()->create();
        $jar        = Jar::factory()->create(['created_by' => $inviter->id]);
        $invitation = JarInvitation::create([
            'jar_id'     => $jar->id,
            'inviter_id' => $inviter->id,
            'invitee_id' => $invitee->id,
            'status'     => 'pending',
        ]);

        // Manually set updated_at in the past to simulate expiration (bypasses Eloquent timestamps)
        \DB::table('jar_invitations')->where('id', $invitation->id)->update([
            'updated_at' => now()->subMinutes(10),
        ]);
        $invitation->refresh();

        $response = $this->actingAs($invitee)->postJson(route('jar-invitations.accept', $invitation));

        $response->assertUnprocessable();
        $this->assertDatabaseMissing('jar_users', ['jar_id' => $jar->id, 'user_id' => $invitee->id]);
    }

    // -------------------------------------------------------------------------
    // Declining invitations
    // -------------------------------------------------------------------------

    public function test_invitee_can_decline_a_pending_invitation(): void
    {
        $inviter    = User::factory()->create();
        $invitee    = User::factory()->create();
        $jar        = Jar::factory()->create(['created_by' => $inviter->id]);
        $invitation = JarInvitation::create([
            'jar_id'     => $jar->id,
            'inviter_id' => $inviter->id,
            'invitee_id' => $invitee->id,
            'status'     => 'pending',
        ]);

        $response = $this->actingAs($invitee)->postJson(route('jar-invitations.decline', $invitation));

        $response->assertOk();
        $this->assertDatabaseHas('jar_invitations', ['id' => $invitation->id, 'status' => 'declined']);
    }
}
