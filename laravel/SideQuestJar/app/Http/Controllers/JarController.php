<?php

namespace App\Http\Controllers;

use App\Models\Jar;
use App\Http\Requests\StoreJarRequest;
use App\Http\Requests\UpdateJarRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class JarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /*
        $jars = Jar::all();
        return view("jars.index", ["jars" => $jars]);
        */
    }
    protected function getOrCreateDailyQuest(Jar $jar)
    {
        $today = now()->toDateString();

        if (
            $jar->current_daily_quest_id &&
            $jar->current_daily_quest_date &&
            $jar->current_daily_quest_date->toDateString() === $today
        ) {
            return $jar->currentDailyQuest;
        }

        $lastQuestId = $jar->current_daily_quest_id;

        $questQuery = $jar->quests();

        if ($lastQuestId && $jar->quests()->count() > 1) {
            $questQuery->where('id', '!=', $lastQuestId);
        }

        $selectedQuest = $questQuery->inRandomOrder()->first();

        if (!$selectedQuest) {
            return null;
        }

        $jar->update([
            'previous_daily_quest_id' => $lastQuestId,
            'current_daily_quest_id' => $selectedQuest->id,
            'current_daily_quest_date' => $today,
        ]);

        $jar->refresh();

        return $jar->currentDailyQuest;
    }

    public function home()
    {
        $mainJar = Jar::where('type', 'official_jar')
            ->where('name', "Today's Adventure")
            ->first();

        $mainQuest = $mainJar ? $this->getOrCreateDailyQuest($mainJar) : null;

        $baseQuery = Jar::where('type', '!=', 'official_jar');

        if (!auth()->check()) {
            $baseQuery->where('type', 'public_jar');
        } else {
            $userId = auth()->id();
            $friendIds = auth()->user()->friends()->pluck('users.id')->toArray();

            $baseQuery->where(function($q) use ($userId, $friendIds) {
                // Public jars are visible to everyone
                $q->where('type', 'public_jar')
                  // My own jars are always visible
                  ->orWhere('created_by', $userId)
                  // Friends only jars where the creator is a friend
                  ->orWhere(function($subQ) use ($friendIds) {
                      $subQ->where('type', 'friends_only_jar')
                           ->whereIn('created_by', $friendIds);
                  })
                  // Invite only jars where I am a participant
                  ->orWhere(function($subQ) use ($userId) {
                      $subQ->where('type', 'invite_only_jar')
                           ->whereHas('users', function($userQ) use ($userId) {
                               $userQ->where('users.id', $userId);
                           });
                  });
            });
        }

        $mostPopularJars = (clone $baseQuery)
            ->with('user')
            ->withCount('users')
            ->orderByDesc('users_count')
            ->get();

        $mostLikedJars = (clone $baseQuery)
            ->with('user')
            ->withCount('likes')
            ->orderByDesc('likes_count')
            ->get();

        $myJars = auth()->check()
            ? Jar::with('user')->where('created_by', auth()->id())->get()
            : collect();

        return view('home', compact(
            'mainJar',
            'mainQuest',
            'mostPopularJars',
            'mostLikedJars',
            'myJars'
        ));
    }

    

    

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        
        $this->authorize('create', Jar::class);
        return view("jars.create");

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJarRequest $request)
    {
        $this->authorize('create', Jar::class);

        $validated = $request->validated();

        $validated['created_by'] = auth()->id();

        if ($request->hasFile('picture')) {
            $validated['picture'] = $request->file('picture')->store('jars', 'public');
        }

        $jar = Jar::create($validated);

        $jar->users()->syncWithoutDetaching([
            auth()->id() => ['streak' => 0]
        ]);

        return redirect()->route('jars.show', $jar)->with('success', 'New jar was added successfully.');
        //Todo a return redirect mehetne a User/show page re ami Profil page kent funkcioanalhatna
    }

    /**
     * Display the specified resource.
     */
    public function show(Jar $jar, $quest = null)
    {
        $jar->load([
            'quests' => fn ($query) => $query->orderBy('id')
        ]);

       
        return view("jars.show", [
            'jar' => $jar,
            'quest' => $quest
        ]);
    }

    

    public function join(Jar $jar){

        $jar->users()->syncWithoutDetaching([
            auth()->id() => ['streak' => '0']
        ]);

        return redirect()->route('jars.show', $jar->id)->with('success', 'You joined the jar');

    }

    public function leave(Jar $jar)
    {
        $jar->users()->detach(auth()->id());

        return redirect()->route('jars.show', $jar->id)->with('success', 'You left the jar');
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Jar $jar)
    {
        return view('jars.edit', compact('jar'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJarRequest $request, Jar $jar)
    {
        $this->authorize('update', $jar);

        $jar->update($request->validated());

        return redirect()->route('jars.show', $jar)->with('success', 'Jar updated successfully');
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jar $jar)
    {
        $this->authorize('delete', $jar);

        $jar->delete();

        return redirect()->route('home')->with('success', 'Jar deleted successfully.');
        //TODO Ha Profil Page kesz lesz vagy mar letezik ott kene a user altal letrehozott Jarokat tarolni igy amikor torol egyet lehet jobb oda vinni nem a Home
    }

    
}
