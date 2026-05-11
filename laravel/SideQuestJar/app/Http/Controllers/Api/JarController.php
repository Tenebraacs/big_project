<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJarRequest;
use App\Http\Requests\UpdateJarRequest;
use App\Models\Jar;
use Illuminate\Http\Request;

class JarController extends Controller
{
    public function index(){
        $jars = Jar::latest()->paginate(10);
        return response()->json($jars);
    }

    public function store(StoreJarRequest $request)
    {
        $validated = $request->validated();
        $validated['created_by'] = auth()->id();

        $jar = Jar::create($validated);

        return response()->json(['msg' => 'Jar added', 'jar' => $jar], 201);
    }

    public function update(UpdateJarRequest $request, Jar $jar)
    {
        $jar->update($request->validated());

        return response()->json(['msg' => 'Jar updated', 'jar' => $jar]);
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
                            ->orderByDesc('likes_count')
                            ->take(10)
                            ->get();

        $recommendedJars = Jar::where('type', '!=', 'official_jar')
                            ->inRandomOrder()
                            ->take(10)
                            ->get();

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

        if ($user) {
            $isJoined = $jar->users()->where('user_id', $user->id)->exists();
        }

        $quests = $jar->quests()->get();

        return response()->json([
            'jar' => $jar,
            'quests' => $quests,
            'is_joined' => $isJoined 
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
}
