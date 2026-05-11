# SideQuestJar — Testing Walkthrough

How to run, understand, and extend the project's test suite.

---

## 1. Quick Start

### Run all tests
```bash
php artisan test
```

### Run a specific test file
```bash
php artisan test --filter=JarTest
php artisan test --filter=FriendTest
php artisan test --filter=MessageTest
php artisan test --filter=JarInvitationTest
```

### Run a single test method
```bash
php artisan test --filter=test_authenticated_user_can_create_a_jar
```

### Run with timing info
```bash
php artisan test --parallel
```

---

## 2. How It Works

### Test Database

Tests use an **in-memory SQLite database** (configured in `phpunit.xml`):

```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

This means:
- Tests **never touch your real MySQL database**
- The database is created fresh for each test class (via `RefreshDatabase`)
- Tests are fast (~2 seconds for all 34)

### The `RefreshDatabase` Trait

Every test class uses `use RefreshDatabase;`. This runs all migrations before each test and rolls back after. **You must include this trait in every new test class.**

```php
class MyNewTest extends TestCase
{
    use RefreshDatabase;  // ← Required!
}
```

### Factories

Tests use **Model Factories** to create test data. These live in `database/factories/`:

| Factory | Creates |
|---|---|
| `UserFactory` | Users with random username, email, profile picture |
| `JarFactory` | Jars with random name, type, and auto-created owner |

**Basic usage:**
```php
// Create and persist a user
$user = User::factory()->create();

// Create with specific attributes
$user = User::factory()->create(['username' => 'TestUser']);

// Create a jar owned by a specific user
$jar = Jar::factory()->create(['created_by' => $user->id, 'type' => 'public_jar']);
```

---

## 3. Test Files Explained

### `JarTest.php` — 10 tests

Tests the full Jar CRUD lifecycle and authorization.

| Test | What it verifies |
|---|---|
| `test_authenticated_user_can_create_a_jar` | Logged-in user can POST to `/jars` and the jar appears in the DB |
| `test_unauthenticated_user_cannot_create_a_jar` | Guest is redirected to `/login` |
| `test_jar_requires_a_name` | Validation rejects missing `name` field |
| `test_jar_name_must_be_unique_per_user` | Same user can't create two jars with the same name |
| `test_jar_creator_can_delete_their_jar` | Owner can DELETE and the jar is soft-deleted |
| `test_non_creator_cannot_delete_a_jar` | Non-owner gets 403 Forbidden |
| `test_user_can_join_a_public_jar` | User can POST to join and appears in `jar_users` table |
| `test_user_cannot_directly_join_an_invite_only_jar` | Invite-only jar rejects direct join with an error message |
| `test_jar_creator_can_update_their_jar` | Owner can PUT to update jar name |
| `test_non_creator_cannot_update_a_jar` | Non-owner gets 403 Forbidden |

**Pattern used — Authorization pair:**
```php
// ✅ Happy path: the authorized user succeeds
public function test_jar_creator_can_delete_their_jar(): void
{
    $user = User::factory()->create();
    $jar  = Jar::factory()->create(['created_by' => $user->id]);

    $response = $this->actingAs($user)->delete(route('jars.destroy', $jar));

    $response->assertRedirect(route('home'));
    $this->assertSoftDeleted('jars', ['id' => $jar->id]);
}

// ❌ Sad path: the unauthorized user is blocked
public function test_non_creator_cannot_delete_a_jar(): void
{
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $jar   = Jar::factory()->create(['created_by' => $owner->id]);

    $response = $this->actingAs($other)->delete(route('jars.destroy', $jar));

    $response->assertForbidden();
    $this->assertNotSoftDeleted('jars', ['id' => $jar->id]);
}
```

---

### `FriendTest.php` — 7 tests

Tests the friend request lifecycle.

| Test | What it verifies |
|---|---|
| `test_user_can_send_a_friend_request_by_username` | POST with a valid username creates a `pending` row in `friends` |
| `test_user_cannot_send_friend_request_to_themselves` | Self-add returns an error |
| `test_user_cannot_send_duplicate_friend_request` | Second request returns an error, table still has 1 row |
| `test_sending_request_to_nonexistent_user_returns_error` | Invalid username returns an error |
| `test_user_can_accept_a_pending_friend_request` | Accepting updates status to `accepted` |
| `test_user_can_decline_a_pending_friend_request` | Declining removes the row from the DB |
| `test_user_can_remove_a_friend` | Removing deletes both reciprocal rows |

---

### `MessageTest.php` — 8 tests

Tests chat messaging with authorization and validation.

| Test | What it verifies |
|---|---|
| `test_user_can_send_a_message_to_a_friend` | POST creates a message row with correct sender/receiver |
| `test_user_cannot_send_a_message_to_a_non_friend` | Non-friend gets 403 |
| `test_message_content_is_required` | Empty content returns 422 |
| `test_message_content_cannot_exceed_1000_characters` | 1001+ chars returns 422 |
| `test_user_can_fetch_messages_with_a_friend` | GET returns a JSON array |
| `test_user_cannot_fetch_messages_with_a_non_friend` | Non-friend gets 403 |
| `test_chat_show_page_is_accessible_for_friends` | Chat page loads (200) for friends |
| `test_chat_show_redirects_when_not_friends` | Chat page redirects non-friends to `/chat` |

**Helper pattern — `makeFriends()`:**
```php
// Reusable method to set up a bidirectional friendship
private function makeFriends(User $a, User $b): void
{
    Friend::create(['user_id' => $a->id, 'friend_id' => $b->id, 'status' => 'accepted']);
    Friend::create(['user_id' => $b->id, 'friend_id' => $a->id, 'status' => 'accepted']);
}
```

> [!TIP]
> Use `postJson()` instead of `post()` when you want validation errors to return **422** (JSON) instead of **302** (redirect). This is important for API-style endpoints.

---

### `JarInvitationTest.php` — 7 tests

Tests the invitation system including expiration.

| Test | What it verifies |
|---|---|
| `test_jar_member_can_invite_a_user` | Creator can send an invite, row created with `pending` status |
| `test_non_member_cannot_invite_a_user` | Non-member gets 403 |
| `test_cannot_invite_user_who_is_already_in_the_jar` | Already-joined user returns 422 |
| `test_invitee_can_accept_a_pending_invitation` | Accept updates status + adds user to `jar_users` |
| `test_non_invitee_cannot_accept_an_invitation` | Stranger gets 403 |
| `test_expired_invitation_cannot_be_accepted` | 10-minute-old invitation returns 422 |
| `test_invitee_can_decline_a_pending_invitation` | Decline updates status to `declined` |

**Pattern — Simulating time expiration:**
```php
// Eloquent auto-manages updated_at, so we bypass it with DB::table()
$invitation = JarInvitation::create([...]);

\DB::table('jar_invitations')->where('id', $invitation->id)->update([
    'updated_at' => now()->subMinutes(10),
]);
$invitation->refresh();  // reload the model from DB
```

---

## 4. Key Assertions Reference

| Assertion | When to use |
|---|---|
| `$response->assertOk()` | Expect HTTP 200 |
| `$response->assertRedirect()` | Expect any redirect (301/302/303/307/308) |
| `$response->assertRedirect(route('home'))` | Expect redirect to a specific URL |
| `$response->assertForbidden()` | Expect HTTP 403 (authorization failed) |
| `$response->assertUnprocessable()` | Expect HTTP 422 (validation failed) |
| `$response->assertSessionHasErrors('name')` | Expect a validation error for the `name` field |
| `$response->assertSessionHas('error')` | Expect a custom error flash message |
| `$this->assertDatabaseHas('table', [...])` | Verify a row exists with these values |
| `$this->assertDatabaseMissing('table', [...])` | Verify a row does NOT exist |
| `$this->assertDatabaseCount('table', N)` | Verify exact row count |
| `$this->assertSoftDeleted('table', [...])` | Verify a row is soft-deleted |
| `$this->assertNotSoftDeleted('table', [...])` | Verify a row is NOT soft-deleted |

---

## 5. How to Add a New Test

### Step 1 — Create the file

```bash
php artisan make:test LikeTest
```

This creates `tests/Feature/LikeTest.php`.

### Step 2 — Add the `RefreshDatabase` trait

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Jar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_like_a_jar(): void
    {
        $user = User::factory()->create();
        $jar  = Jar::factory()->create();

        $response = $this->actingAs($user)->post("/jars/{$jar->id}/like");

        $response->assertOk();
        $this->assertDatabaseHas('likes', [
            'user_id'       => $user->id,
            'likeable_id'   => $jar->id,
            'likeable_type' => Jar::class,
        ]);
    }
}
```

### Step 3 — Follow the pattern

For every feature, write at least:

1. **✅ Happy path** — the action succeeds for an authorized user
2. **❌ Unauthorized path** — a non-authorized user gets 403
3. **❌ Validation path** — invalid input gets rejected (422 or session errors)

### Step 4 — Run and verify

```bash
php artisan test --filter=LikeTest
```

---

## 6. Current Test Coverage

```
Tests:    34 passed (63 assertions)
Duration: ~2s
```

| File | Tests | Area |
|---|---|---|
| `ExampleTest` | 2 | App boots, home page loads |
| `JarTest` | 10 | CRUD + join authorization |
| `FriendTest` | 7 | Friend request lifecycle |
| `MessageTest` | 8 | Chat auth + validation |
| `JarInvitationTest` | 7 | Invite lifecycle + expiration |

> [!IMPORTANT]
> Always run `php artisan test` before pushing to make sure nothing is broken. The full suite takes under 2 seconds.
