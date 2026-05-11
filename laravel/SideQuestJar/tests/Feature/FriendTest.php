<?php

namespace Tests\Feature;

use App\Models\Friend;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FriendTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // Sending requests
    // -------------------------------------------------------------------------

    public function test_user_can_send_a_friend_request_by_username(): void
    {
        $sender   = User::factory()->create();
        $receiver = User::factory()->create(['username' => 'testuser']);

        $response = $this->actingAs($sender)->post(route('friends.add'), [
            'username' => 'testuser',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('friends', [
            'user_id'   => $sender->id,
            'friend_id' => $receiver->id,
            'status'    => 'pending',
        ]);
    }

    public function test_user_cannot_send_friend_request_to_themselves(): void
    {
        $user = User::factory()->create(['username' => 'myself']);

        $response = $this->actingAs($user)->post(route('friends.add'), [
            'username' => 'myself',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('friends', ['user_id' => $user->id, 'friend_id' => $user->id]);
    }

    public function test_user_cannot_send_duplicate_friend_request(): void
    {
        $sender   = User::factory()->create();
        $receiver = User::factory()->create(['username' => 'receiver']);

        Friend::create(['user_id' => $sender->id, 'friend_id' => $receiver->id, 'status' => 'pending']);

        $response = $this->actingAs($sender)->post(route('friends.add'), [
            'username' => 'receiver',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('friends', 1);
    }

    public function test_sending_request_to_nonexistent_user_returns_error(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('friends.add'), [
            'username' => 'does_not_exist',
        ]);

        $response->assertSessionHas('error');
    }

    // -------------------------------------------------------------------------
    // Accepting / declining
    // -------------------------------------------------------------------------

    public function test_user_can_accept_a_pending_friend_request(): void
    {
        $sender   = User::factory()->create();
        $receiver = User::factory()->create();

        Friend::create(['user_id' => $sender->id, 'friend_id' => $receiver->id, 'status' => 'pending']);

        $response = $this->actingAs($receiver)->post(route('friends.accept', $sender));

        $response->assertRedirect();
        $this->assertDatabaseHas('friends', [
            'user_id'   => $sender->id,
            'friend_id' => $receiver->id,
            'status'    => 'accepted',
        ]);
    }

    public function test_user_can_decline_a_pending_friend_request(): void
    {
        $sender   = User::factory()->create();
        $receiver = User::factory()->create();

        Friend::create(['user_id' => $sender->id, 'friend_id' => $receiver->id, 'status' => 'pending']);

        $response = $this->actingAs($receiver)->post(route('friends.decline', $sender));

        $response->assertRedirect();
        $this->assertDatabaseMissing('friends', [
            'user_id'   => $sender->id,
            'friend_id' => $receiver->id,
        ]);
    }

    // -------------------------------------------------------------------------
    // Removing friends
    // -------------------------------------------------------------------------

    public function test_user_can_remove_a_friend(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        Friend::create(['user_id' => $userA->id, 'friend_id' => $userB->id, 'status' => 'accepted']);
        Friend::create(['user_id' => $userB->id, 'friend_id' => $userA->id, 'status' => 'accepted']);

        $response = $this->actingAs($userA)->delete(route('friends.destroy', $userB));

        $response->assertRedirect();
        $this->assertDatabaseMissing('friends', ['user_id' => $userA->id, 'friend_id' => $userB->id]);
        $this->assertDatabaseMissing('friends', ['user_id' => $userB->id, 'friend_id' => $userA->id]);
    }
}
