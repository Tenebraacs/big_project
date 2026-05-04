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

        return view('chat.show', compact('user', 'users'));
    }

    public function fetchMessages(User $user)
    {
        $messages = Message::where(function ($q) use ($user) {
            $q->where('sender_id', auth()->id())->where('receiver_id', $user->id);
        })->orWhere(function ($q) use ($user) {
            $q->where('sender_id', $user->id)->where('receiver_id', auth()->id());
        })->orderBy('created_at', 'asc')->get();

        Message::where('sender_id', $user->id)
            ->where('receiver_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json($messages);
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
