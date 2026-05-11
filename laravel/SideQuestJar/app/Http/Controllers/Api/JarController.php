<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJarRequest;
use App\Http\Requests\UpdateJarRequest;
use App\Models\Jar;
use App\Models\JarQuest;
use Illuminate\Http\Request;

class JarController extends Controller
{
    public function popularJars(Request $request)
    {
        $jars = Jar::where('type', '!=', 'official_jar')
            ->withCount('likes')
            ->with('user:id,username')
            ->orderBy('likes_count', 'desc')
            ->paginate(10);
            
        $jars->getCollection()->transform(function ($jar) {
            $jar->likes = $jar->likes_count;
            return $jar;
        });

        return response()->json($jars);
    }

    public function recommendedJars(Request $request)
    {
        $jars = Jar::where('type', '!=', 'official_jar')
            ->withCount('likes')
            ->with('user:id,username')
            ->latest()
            ->paginate(10);
            
        $jars->getCollection()->transform(function ($jar) {
            $jar->likes = $jar->likes_count;
            return $jar;
        });

        return response()->json($jars);
    }

    public function index(){
        $jars = Jar::latest()->paginate(10);
        return response()->json($jars);
    }

    public function store(StoreJarRequest $request)
    {
        $validated = $request->validated();
        $validated['created_by'] = $request->user()->id;

        if ($request->hasFile('picture')) {
            $validated['picture'] = $request->file('picture')->store('jars', 'public');
        }

        $jar = Jar::create($validated);

        // Auto-join the creator
        $jar->users()->syncWithoutDetaching([
            $request->user()->id => ['streak' => 0],
        ]);

        return response()->json($jar, 201);
    }

    public function update(UpdateJarRequest $request, Jar $jar)
    {
        $validated = $request->validated();

        if ($request->hasFile('picture')) {
            $validated['picture'] = $request->file('picture')->store('jars', 'public');
        }

        $jar->update($validated);

        return response()->json(['msg' => 'Jar updated successfully', 'jar' => $jar]);
    }

    public function destroy(Jar $jar)
    {
        $this->authorize('delete', $jar);

        $jar->delete();

        return response()->json(['msg' => 'Jar deleted']);
    }

    public function discover() 
    {
        $mainJar = Jar::where('type', 'official_jar')->first();
        $mainQuest = $mainJar ? $mainJar->quests()->inRandomOrder()->first() : null;

        $popularJars = Jar::where('type', '!=', 'official_jar')
                            ->withCount('likes')
                            ->with('user:id,username')
                            ->orderByDesc('likes_count')
                            ->take(10)
                            ->get()
                            ->map(function ($jar) {
                                $jar->likes = $jar->likes_count;
                                return $jar;
                            });

        $recommendedJars = Jar::where('type', '!=', 'official_jar')
                            ->withCount('likes')
                            ->with('user:id,username')
                            ->inRandomOrder()
                            ->take(10)
                            ->get()
                            ->map(function ($jar) {
                                $jar->likes = $jar->likes_count;
                                return $jar;
                            });

        return response()->json([
            'mainJar' => $mainJar,
            'mainQuest' => $mainQuest,
            'popularJars' => $popularJars,
            'recommendedJars' => $recommendedJars
        ]);
    }

    public function show(Request $request, Jar $jar)
    {
        $isJoined = false;
        $user = $request->user();

        $jar->loadCount('likes');
        $jar->setAttribute('likes', $jar->likes_count);
        $jar->setAttribute(
            'is_liked',
            $user ? $jar->likes()->where('user_id', $user->id)->exists() : false
        );

        if ($user) {
            $isJoined = $jar->users()->where('user_id', $user->id)->exists();
        }

        // Mark quests with whether the user already completed them today
        $quests = $jar->quests()->get()->map(function ($quest) use ($user) {
            $completedToday = false;
            if ($user) {
                $completedToday = $quest->completions()
                    ->where('user_id', $user->id)
                    ->whereDate('completion_date', today())
                    ->exists();
            }
            $data = $quest->toArray();
            $data['completed_today'] = $completedToday;
            return $data;
        });

        $members = $jar->users()
            ->select('users.id', 'users.username', 'users.profile_picture')
            ->withPivot('streak')
            ->orderByPivot('streak', 'desc')
            ->get()
            ->map(fn($u) => [
                'id'              => $u->id,
                'username'        => $u->username,
                'profile_picture' => $u->profile_picture,
                'streak'          => $u->pivot->streak,
                'is_you'          => $user && $u->id === $user->id,
            ]);

        return response()->json([
            'jar'                    => $jar,
            'quests'                 => $quests,
            'is_joined'              => $isJoined,
            'members'                => $members,
            'current_daily_quest_id' => $jar->current_daily_quest_id,
        ]);
    }


    public function join(Request $request, Jar $jar)
    {
        $user = $request->user();

        if ($jar->users()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'You have already joined this jar.'], 409);
        }

        $jar->users()->attach($user->id, ['streak' => 0]);

        return response()->json(['message' => 'Successfully joined the jar.']);
    }

    public function leave(Request $request, Jar $jar)
    {
        $user = $request->user();

        if (! $jar->users()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'You are not a member of this jar.'], 404);
        }

        $jar->users()->detach($user->id);

        return response()->json(['message' => 'Successfully left the jar.']);
    }

    public function userJars(Request $request)
    {
        $user = $request->user();
        $jars = Jar::where('created_by', $user->id)
            ->withCount('likes')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($jar) {
                $jar->likes = $jar->likes_count;
                return $jar;
            });

        return response()->json($jars);
    }

    public function invite(Request $request)
    {
        $request->validate([
            'jar_id' => 'required|exists:jars,id',
            'invitee_id' => 'required|exists:users,id',
        ]);

        $jar = Jar::findOrFail($request->jar_id);
        
        // Ensure inviter is in the jar or is the creator
        if ($jar->created_by !== $request->user()->id && !$jar->users()->where('user_id', $request->user()->id)->exists()) {
            return response()->json(['message' => 'You cannot invite others to this jar.'], 403);
        }

        // Check if already invited or already a member
        $existing = \App\Models\JarInvitation::where('jar_id', $request->jar_id)
            ->where('invitee_id', $request->invitee_id)
            ->where('status', 'pending')
            ->exists();

        if ($existing || $jar->users()->where('user_id', $request->invitee_id)->exists()) {
            return response()->json(['message' => 'User is already invited or a member.'], 409);
        }

        \App\Models\JarInvitation::create([
            'jar_id' => $request->jar_id,
            'inviter_id' => $request->user()->id,
            'invitee_id' => $request->invitee_id,
            'status' => 'pending'
        ]);

        return response()->json(['message' => 'Invitation sent successfully.']);
    }

    public function storeQuest(Request $request, Jar $jar)
    {
        $this->authorize('update', $jar);

        $validated = $request->validate([
            'title' => 'required|string|max:60',
            'description' => 'nullable|string|max:1500',
            'picture' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        if ($request->hasFile('picture')) {
            $validated['picture'] = $request->file('picture')->store('quests', 'public');
        }
        
        $validated['created_by'] = $request->user()->id;

        $quest = $jar->quests()->create($validated);

        return response()->json([
            'message' => 'Quest created successfully.',
            'quest' => $quest
        ], 201);
    }

    public function updateQuest(Request $request, Jar $jar, JarQuest $quest)
    {
        $this->authorize('update', $jar);

        if ($quest->jar_id !== $jar->id) {
            return response()->json(['message' => 'Quest does not belong to this jar.'], 404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:60',
            'description' => 'nullable|string|max:1500',
            'picture' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        if ($request->hasFile('picture')) {
            $validated['picture'] = $request->file('picture')->store('quests', 'public');
        }

        $quest->update($validated);

        return response()->json([
            'message' => 'Quest updated successfully.',
            'quest' => $quest
        ]);
    }

    public function destroyQuest(Jar $jar, JarQuest $quest)
    {
        $this->authorize('update', $jar);

        if ($quest->jar_id !== $jar->id) {
            return response()->json(['message' => 'Quest does not belong to this jar.'], 404);
        }

        $quest->delete();

        return response()->json(['message' => 'Quest deleted successfully.']);
    }
}
