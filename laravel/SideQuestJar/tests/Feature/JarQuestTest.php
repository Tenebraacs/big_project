<?php

namespace Tests\Feature;

use App\Models\Jar;
use App\Models\JarQuest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JarQuestTest extends TestCase
{
    use RefreshDatabase;

    public function test_jar_creator_can_create_a_quest(): void
    {
        $user = User::factory()->create();
        $jar = Jar::factory()->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)->post(route('jars.quests.store', $jar), [
            'title' => 'Test Quest',
            'description' => 'This is a test quest description.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('jar_quests', [
            'jar_id' => $jar->id,
            'title' => 'Test Quest',
        ]);
    }

    public function test_non_creator_cannot_create_a_quest(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $jar = Jar::factory()->create(['created_by' => $owner->id]);

        $response = $this->actingAs($other)->post(route('jars.quests.store', $jar), [
            'title' => 'Test Quest',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('jar_quests', [
            'title' => 'Test Quest',
        ]);
    }

    public function test_quest_requires_a_title(): void
    {
        $user = User::factory()->create();
        $jar = Jar::factory()->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)->post(route('jars.quests.store', $jar), [
            'title' => '',
        ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_jar_creator_can_update_a_quest(): void
    {
        $user = User::factory()->create();
        $jar = Jar::factory()->create(['created_by' => $user->id]);
        $quest = JarQuest::factory()->create(['jar_id' => $jar->id]);

        $response = $this->actingAs($user)->put(route('jars.quests.update', [$jar, $quest]), [
            'title' => 'Updated Quest',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('jar_quests', [
            'id' => $quest->id,
            'title' => 'Updated Quest',
        ]);
    }

    public function test_jar_creator_can_delete_a_quest(): void
    {
        $user = User::factory()->create();
        $jar = Jar::factory()->create(['created_by' => $user->id]);
        $quest = JarQuest::factory()->create(['jar_id' => $jar->id]);

        $response = $this->actingAs($user)->delete(route('jars.quests.destroy', [$jar, $quest]));

        $response->assertRedirect();
        $this->assertSoftDeleted('jar_quests', [
            'id' => $quest->id,
        ]);
    }
}
