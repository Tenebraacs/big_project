<?php

namespace App\Http\Controllers\Api;

use App\Models\JarComment;
use App\Models\JarQuest;
use App\Models\QuestCompletion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class JarInteractionController extends Controller
{
    // ─── Comments ────────────────────────────────────────────────────────────

    public function getComments(Request $request, $jarId)
    {
        $comments = JarComment::where('jar_id', $jarId)
            ->with('user:id,username,profile_picture')
            ->latest()
            ->take(50)
            ->get()
            ->map(fn($c) => [
                'id'              => $c->id,
                'content'         => $c->content,
                'created_at'      => $c->created_at->toISOString(),
                'user_id'         => $c->user_id,
                'username'        => $c->user?->username ?? 'unknown',
                'profile_picture' => $c->user?->profile_picture,
            ]);

        return response()->json(['comments' => $comments]);
    }

    public function postComment(Request $request, $jarId)
    {
        $request->validate(['content' => 'required|string|max:500']);

        $comment = JarComment::create([
            'jar_id'  => $jarId,
            'user_id' => $request->user()->id,
            'content' => $request->content,
        ]);

        $comment->load('user:id,username,profile_picture');

        return response()->json([
            'id'              => $comment->id,
            'content'         => $comment->content,
            'created_at'      => $comment->created_at->toISOString(),
            'user_id'         => $comment->user_id,
            'username'        => $comment->user?->username ?? 'unknown',
            'profile_picture' => $comment->user?->profile_picture,
        ], 201);
    }

    // ─── Quest Completion ────────────────────────────────────────────────────

    public function completeQuest(Request $request, JarQuest $quest)
    {
        $user = $request->user();

        // Prevent double-completion
        $alreadyDone = false;
        if ($quest->jar && $quest->jar->type === 'official_jar') {
            $alreadyDone = QuestCompletion::where('jar_quest_id', $quest->id)
                ->where('user_id', $user->id)
                ->whereDate('completion_date', today())
                ->exists();
        } else {
            $alreadyDone = QuestCompletion::where('jar_quest_id', $quest->id)
                ->where('user_id', $user->id)
                ->exists();
        }

        if ($alreadyDone) {
            return response()->json(['message' => 'Already completed.'], 409);
        }

        // Handle optional image upload
        $picturePath = null;
        if ($request->hasFile('picture')) {
            $picturePath = $request->file('picture')->store('completions', 'public');
        }

        $completion = QuestCompletion::create([
            'jar_quest_id'    => $quest->id,
            'user_id'         => $user->id,
            'completion_date' => now(),
            'type'            => 'public_post',
            'picture'         => $picturePath,
            'description'     => $request->input('description'),
        ]);

        // Increment streak for the user in this jar
        $jar = $quest->jar;
        if ($jar) {
            $jar->users()->updateExistingPivot($user->id, [
                'streak' => \DB::raw('streak + 1'),
            ]);
        }

        // Award XP
        if ($quest->xp_gain && $quest->xp_gain > 0) {
            $user->increment('user_xp', $quest->xp_gain);
        }

        $completion->load('user:id,username,profile_picture');

        return response()->json([
            'message'    => 'Quest completed! +' . ($quest->xp_gain ?? 0) . ' XP',
            'completion' => [
                'id'              => $completion->id,
                'description'     => $completion->description,
                'picture_url'     => $completion->picture_url,
                'completed_at'    => $completion->completion_date->toISOString(),
                'username'        => $completion->user?->username ?? 'unknown',
                'profile_picture' => $completion->user?->profile_picture,
                'xp_gain'         => $quest->xp_gain ?? 0,
            ],
        ], 201);
    }

    // ─── Get Completions for a Jar ───────────────────────────────────────────

    public function getCompletions(Request $request, $jarId)
    {
        $completions = QuestCompletion::whereHas('jarQuest', fn($q) => $q->where('jar_id', $jarId))
            ->with([
                'user:id,username,profile_picture',
                'jarQuest:id,title,xp_gain',
            ])
            ->latest('completion_date')
            ->take(30)
            ->get()
            ->map(fn($c) => [
                'id'              => $c->id,
                'description'     => $c->description,
                'picture_url'     => $c->picture_url,
                'completed_at'    => $c->completion_date->toISOString(),
                'username'        => $c->user?->username ?? 'unknown',
                'profile_picture' => $c->user?->profile_picture,
                'quest_title'     => $c->jarQuest?->title ?? '',
                'xp_gain'         => $c->jarQuest?->xp_gain ?? 0,
            ]);

        return response()->json(['completions' => $completions]);
    }
}
