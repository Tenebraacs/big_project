<?php

namespace Tests\Feature;

use App\Models\Friend;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageTest extends TestCase
{
    use RefreshDatabase;

    private function makeFriends(User $a, User $b): void
    {
        Friend::create(['user_id' => $a->id, 'friend_id' => $b->id, 'status' => 'accepted']);
        Friend::create(['user_id' => $b->id, 'friend_id' => $a->id, 'status' => 'accepted']);
    }

    // -------------------------------------------------------------------------
    // Sending messages
    // -------------------------------------------------------------------------

    public function test_user_can_send_a_message_to_a_friend(): void
    {
        $sender   = User::factory()->create();
        $receiver = User::factory()->create();
        $this->makeFriends($sender, $receiver);

        $response = $this->actingAs($sender)->post(route('chat.store', $receiver), [
            'content' => 'Hello there!',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('messages', [
            'sender_id'   => $sender->id,
            'receiver_id' => $receiver->id,
            'content'     => 'Hello there!',
        ]);
    }

    public function test_user_cannot_send_a_message_to_a_non_friend(): void
    {
        $sender   = User::factory()->create();
        $stranger = User::factory()->create();

        $response = $this->actingAs($sender)->post(route('chat.store', $stranger), [
            'content' => 'Spam!',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('messages', [
            'sender_id'   => $sender->id,
            'receiver_id' => $stranger->id,
        ]);
    }

    public function test_message_content_is_required(): void
    {
        $sender   = User::factory()->create();
        $receiver = User::factory()->create();
        $this->makeFriends($sender, $receiver);

        $response = $this->actingAs($sender)->postJson(route('chat.store', $receiver), [
            'content' => '',
        ]);

        $response->assertUnprocessable();
    }

    public function test_message_content_cannot_exceed_1000_characters(): void
    {
        $sender   = User::factory()->create();
        $receiver = User::factory()->create();
        $this->makeFriends($sender, $receiver);

        $response = $this->actingAs($sender)->postJson(route('chat.store', $receiver), [
            'content' => str_repeat('a', 1001),
        ]);

        $response->assertUnprocessable();
    }

    // -------------------------------------------------------------------------
    // Fetching messages
    // -------------------------------------------------------------------------

    public function test_user_can_fetch_messages_with_a_friend(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $this->makeFriends($userA, $userB);

        $response = $this->actingAs($userA)->getJson(route('chat.fetch', $userB));

        $response->assertOk()->assertJsonIsArray();
    }

    public function test_user_cannot_fetch_messages_with_a_non_friend(): void
    {
        $userA    = User::factory()->create();
        $stranger = User::factory()->create();

        $response = $this->actingAs($userA)->getJson(route('chat.fetch', $stranger));

        $response->assertForbidden();
    }

    // -------------------------------------------------------------------------
    // Chat page access
    // -------------------------------------------------------------------------

    public function test_chat_show_page_is_accessible_for_friends(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $this->makeFriends($userA, $userB);

        $response = $this->actingAs($userA)->get(route('chat.show', $userB));

        $response->assertOk();
    }

    public function test_chat_show_redirects_when_not_friends(): void
    {
        $userA    = User::factory()->create();
        $stranger = User::factory()->create();

        $response = $this->actingAs($userA)->get(route('chat.show', $stranger));

        $response->assertRedirect(route('chat.index'));
    }
}
