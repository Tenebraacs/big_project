<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Message;
use App\Models\JarInvitation;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function fetchMessages(User $user)
    {
        // Authorize the action
        abort_unless($user->id === auth()->id() || auth()->user()->friends()->where('users.id', $user->id)->exists(), 403, 'You can only view messages of friends.');

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

        $invitations = JarInvitation::with(['jar' => function($q) { $q->withTrashed(); }, 'invitee' => function($q) { $q->withTrashed(); }])->where(function ($q) use ($user) {
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
        // Authorize the action
        abort_unless($user->id === auth()->id() || auth()->user()->friends()->where('users.id', $user->id)->exists(), 403, 'You can only message friends.');

        $request->validate(['content' => 'required|string|max:1000']);

        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $user->id,
            'content' => $request->content,
        ]);

        return response()->json([
            'type' => 'chat',
            'id' => $message->id,
            'sender_id' => $message->sender_id,
            'content' => $message->content,
            'created_at' => $message->created_at,
        ]);
    }
}
