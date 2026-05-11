<?php

namespace Tests\Feature;

use App\Models\Jar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JarTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // Creating jars
    // -------------------------------------------------------------------------

    public function test_authenticated_user_can_create_a_jar(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('jars.store'), [
            'name'        => 'My Test Jar',
            'description' => 'A description.',
            'type'        => 'public_jar',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('jars', ['name' => 'My Test Jar', 'created_by' => $user->id]);
    }

    public function test_unauthenticated_user_cannot_create_a_jar(): void
    {
        $response = $this->post(route('jars.store'), [
            'name' => 'Guest Jar',
            'type' => 'public_jar',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseMissing('jars', ['name' => 'Guest Jar']);
    }

    public function test_jar_requires_a_name(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('jars.store'), [
            'type' => 'public_jar',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_jar_name_must_be_unique_per_user(): void
    {
        $user = User::factory()->create();
        Jar::factory()->create(['name' => 'Duplicate', 'created_by' => $user->id, 'type' => 'public_jar']);

        $response = $this->actingAs($user)->post(route('jars.store'), [
            'name' => 'Duplicate',
            'type' => 'public_jar',
        ]);

        $response->assertSessionHasErrors('name');
    }

    // -------------------------------------------------------------------------
    // Deleting jars
    // -------------------------------------------------------------------------

    public function test_jar_creator_can_delete_their_jar(): void
    {
        $user = User::factory()->create();
        $jar  = Jar::factory()->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)->delete(route('jars.destroy', $jar));

        $response->assertRedirect(route('home'));
        $this->assertSoftDeleted('jars', ['id' => $jar->id]);
    }

    public function test_non_creator_cannot_delete_a_jar(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $jar   = Jar::factory()->create(['created_by' => $owner->id]);

        $response = $this->actingAs($other)->delete(route('jars.destroy', $jar));

        $response->assertForbidden();
        $this->assertNotSoftDeleted('jars', ['id' => $jar->id]);
    }

    // -------------------------------------------------------------------------
    // Joining jars
    // -------------------------------------------------------------------------

    public function test_user_can_join_a_public_jar(): void
    {
        $user = User::factory()->create();
        $jar  = Jar::factory()->create(['type' => 'public_jar']);

        $response = $this->actingAs($user)->post(route('jars.join', $jar));

        $response->assertRedirect();
        $this->assertDatabaseHas('jar_users', ['jar_id' => $jar->id, 'user_id' => $user->id]);
    }

    public function test_user_cannot_directly_join_an_invite_only_jar(): void
    {
        $user = User::factory()->create();
        $jar  = Jar::factory()->create(['type' => 'invite_only_jar']);

        $response = $this->actingAs($user)->post(route('jars.join', $jar));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('jar_users', ['jar_id' => $jar->id, 'user_id' => $user->id]);
    }

    // -------------------------------------------------------------------------
    // Updating jars
    // -------------------------------------------------------------------------

    public function test_jar_creator_can_update_their_jar(): void
    {
        $user = User::factory()->create();
        $jar  = Jar::factory()->create(['created_by' => $user->id, 'name' => 'Old Name']);

        $response = $this->actingAs($user)->put(route('jars.update', $jar), [
            'name' => 'New Name',
            'type' => $jar->type,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('jars', ['id' => $jar->id, 'name' => 'New Name']);
    }

    public function test_non_creator_cannot_update_a_jar(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $jar   = Jar::factory()->create(['created_by' => $owner->id]);

        $response = $this->actingAs($other)->put(route('jars.update', $jar), [
            'name' => 'Hacked Name',
            'type' => $jar->type,
        ]);

        $response->assertForbidden();
    }
}
