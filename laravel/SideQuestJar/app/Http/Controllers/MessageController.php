<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        $users = auth()->user()->friends;
        return view('chat.index', compact('users'));
    }

    public function show(User $user)
    {
        $isFriend = auth()->user()->friends()->where('users.id', $user->id)->exists();
        if (!$isFriend) {
            return redirect()->route('chat.index')->with('error', 'You can only message friends.');
        }

        $users = auth()->user()->friends;

        // All jars I'm in (joined + created)
        $myJars = auth()->user()->jars()->get()
            ->merge(auth()->user()->createdJars()->get())
            ->unique('id');

        // Jar IDs the invitee is already part of (as member or creator)
        $inviteeJarIds = $user->jars()->pluck('jars.id')
            ->merge($user->createdJars()->pluck('id'))
            ->unique()
            ->values()
            ->toArray();

        return view('chat.show', compact('user', 'users', 'myJars', 'inviteeJarIds'));
    }

    public function fetchMessages(User $user)
    {
        $messages = Message::where(function ($q) use ($user) {
            $q->where('sender_id', auth()->id())->where('receiver_id', $user->id);
        })->orWhere(function ($q) use ($user) {
            $q->where('sender_id', $user->id)->where('receiver_id', auth()->id());
        })->get()->map(function ($msg) {
            return [
                'type' => 'chat',
                'id' => $msg->id,
                'sender_id' => $msg->sender_id,
                'content' => $msg->content,
                'created_at' => $msg->created_at,
            ];
        });

        $invitations = \App\Models\JarInvitation::with(['jar' => function($q) { $q->withTrashed(); }, 'invitee' => function($q) { $q->withTrashed(); }])->where(function ($q) use ($user) {
            $q->where('inviter_id', auth()->id())->where('invitee_id', $user->id);
        })->orWhere(function ($q) use ($user) {
            $q->where('inviter_id', $user->id)->where('invitee_id', auth()->id());
        })->get()->map(function ($inv) {
            return [
                'type' => 'invite',
                'id' => $inv->id,
                'sender_id' => $inv->inviter_id,
                'content' => 'Invite sent to ' . ($inv->invitee ? $inv->invitee->username : 'Unknown User'),
                'jar_name' => $inv->jar ? $inv->jar->name : 'Deleted Jar',
                'jar_thumb' => $inv->jar ? $inv->jar->picture_url : '',
                'status' => $inv->status,
                'created_at' => $inv->created_at,
                'updated_at' => $inv->updated_at,
            ];
        });

        $timeline = $messages->concat($invitations)->sortBy('created_at')->values();

        Message::where('sender_id', $user->id)
            ->where('receiver_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json($timeline);
    }

    public function store(Request $request, User $user)
    {
        $request->validate(['content' => 'required|string|max:1000']);

        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $user->id,
            'content' => $request->content,
        ]);

        return response()->json($message);
    }
}
