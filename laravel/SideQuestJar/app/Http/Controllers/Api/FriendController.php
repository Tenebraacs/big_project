<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Friend;
use App\Models\User;
use Illuminate\Http\Request;

class FriendController extends Controller
{
    /**
     * GET /api/friends
     * Returns the authenticated user's friends, pending incoming requests,
     * and pending outgoing requests in a single payload (BFF: aggregated response).
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $friends = $user->friends()->get(['users.id', 'users.username', 'users.profile_picture']);
        $friends->each(fn($u) => $u->append('profile_picture_url'));

        $pendingReceived = $user->friendRequestsReceived()
            ->with(['user:id,username,profile_picture'])
            ->get()
            ->map(fn($f) => [
                'id'                  => $f->user->id,
                'username'            => $f->user->username,
                'profile_picture'     => $f->user->profile_picture,
                'profile_picture_url' => $f->user->profile_picture_url,
            ]);

        $pendingSent = $user->friendRequestsSent()
            ->with(['friend:id,username,profile_picture'])
            ->get()
            ->map(fn($f) => [
                'id'                  => $f->friend->id,
                'username'            => $f->friend->username,
                'profile_picture'     => $f->friend->profile_picture,
                'profile_picture_url' => $f->friend->profile_picture_url,
            ]);

        return response()->json([
            'friends'          => $friends,
            'pending_received' => $pendingReceived,
            'pending_sent'     => $pendingSent,
        ]);
    }

    /**
     * POST /api/friends/add
     * Send a friend request by username.
     */
    public function addByUsername(Request $request)
    {
        $request->validate(['username' => 'required|string']);

        $target = User::where('username', $request->username)->first();

        if (!$target) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        return $this->store($request, $target);
    }

    /**
     * POST /api/friends/{user}
     * Send a friend request to a specific user (by ID).
     */
    public function store(Request $request, User $user)
    {
        $authId = $request->user()->id;

        if ($authId === $user->id) {
            return response()->json(['message' => 'You cannot add yourself as a friend.'], 422);
        }

        $existing = Friend::where(function ($q) use ($authId, $user) {
            $q->where('user_id', $authId)->where('friend_id', $user->id);
        })->orWhere(function ($q) use ($authId, $user) {
            $q->where('user_id', $user->id)->where('friend_id', $authId);
        })->first();

        if ($existing) {
            return response()->json(['message' => 'A friend request already exists between you two.'], 409);
        }

        Friend::create([
            'user_id'   => $authId,
            'friend_id' => $user->id,
            'status'    => 'pending',
        ]);

        return response()->json(['message' => 'Friend request sent.'], 201);
    }

    /**
     * POST /api/friends/{user}/accept
     * Accept a pending incoming request from the given user.
     */
    public function accept(Request $request, User $user)
    {
        $authId = $request->user()->id;

        $friendRequest = Friend::where('user_id', $user->id)
            ->where('friend_id', $authId)
            ->where('status', 'pending')
            ->first();

        if (!$friendRequest) {
            return response()->json(['message' => 'Friend request not found.'], 404);
        }

        $friendRequest->update(['status' => 'accepted']);

        // Create the reciprocal accepted entry so both sides see each other as friends
        Friend::firstOrCreate(
            ['user_id' => $authId, 'friend_id' => $user->id],
            ['status'  => 'accepted']
        );

        return response()->json(['message' => 'Friend request accepted.']);
    }

    /**
     * POST /api/friends/{user}/decline
     * Decline a pending incoming request.
     */
    public function decline(Request $request, User $user)
    {
        $authId = $request->user()->id;

        Friend::where('user_id', $user->id)
            ->where('friend_id', $authId)
            ->where('status', 'pending')
            ->delete();

        return response()->json(['message' => 'Friend request declined.']);
    }

    /**
     * DELETE /api/friends/{user}
     * Remove an existing friendship (both directions).
     */
    public function destroy(Request $request, User $user)
    {
        $authId = $request->user()->id;

        Friend::where(function ($q) use ($authId, $user) {
            $q->where('user_id', $authId)->where('friend_id', $user->id);
        })->orWhere(function ($q) use ($authId, $user) {
            $q->where('user_id', $user->id)->where('friend_id', $authId);
        })->delete();

        return response()->json(['message' => 'Friend removed.']);
    }
}
