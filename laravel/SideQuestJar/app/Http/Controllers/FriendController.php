<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Friend;
use Illuminate\Http\Request;

class FriendController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $friends = $user->friends;
        $pendingRequests = $user->friendRequestsReceived()->with('user')->get();
        $sentRequests = $user->friendRequestsSent()->with('friend')->get();

        return view('friends.index', compact('friends', 'pendingRequests', 'sentRequests'));
    }

    public function addByUsername(Request $request)
    {
        $request->validate([
            'username' => 'required|string'
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user) {
            return back()->with('error', 'User not found.');
        }

        return $this->store($request, $user);
    }

    public function store(Request $request, User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot add yourself as a friend.');
        }

        // Check if a request already exists
        $existing = Friend::where(function ($q) use ($user) {
            $q->where('user_id', auth()->id())->where('friend_id', $user->id);
        })->orWhere(function ($q) use ($user) {
            $q->where('user_id', $user->id)->where('friend_id', auth()->id());
        })->first();

        if ($existing) {
            return back()->with('error', 'Friend request already exists.');
        }

        Friend::create([
            'user_id' => auth()->id(),
            'friend_id' => $user->id,
            'status' => 'pending'
        ]);

        return back()->with('success', 'Friend request sent.');
    }

    public function accept(User $user)
    {
        $request = Friend::where('user_id', $user->id)
            ->where('friend_id', auth()->id())
            ->where('status', 'pending')
            ->first();

        if ($request) {
            $request->update(['status' => 'accepted']);
            // Create reciprocal connection
            Friend::firstOrCreate([
                'user_id' => auth()->id(),
                'friend_id' => $user->id,
            ], [
                'status' => 'accepted'
            ]);

            return back()->with('success', 'Friend request accepted.');
        }

        return back()->with('error', 'Friend request not found.');
    }

    public function decline(User $user)
    {
        Friend::where('user_id', $user->id)
            ->where('friend_id', auth()->id())
            ->where('status', 'pending')
            ->delete();

        return back()->with('success', 'Friend request declined.');
    }

    public function destroy(User $user)
    {
        Friend::where(function ($q) use ($user) {
            $q->where('user_id', auth()->id())->where('friend_id', $user->id);
        })->orWhere(function ($q) use ($user) {
            $q->where('user_id', $user->id)->where('friend_id', auth()->id());
        })->delete();

        return back()->with('success', 'Friend removed.');
    }
}
