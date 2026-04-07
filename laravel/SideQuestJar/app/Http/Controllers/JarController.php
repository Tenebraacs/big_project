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
        $jars = Jar::all();
        return view("jars.index", ["jars" => $jars]);
    }

    public function home(){
        //Lekeri az elso type == official_jar adatait
        $mainJar = Jar::where('type', 'official_jar')->where('name', "Today's Adventure")->first();
        $mainQuest = $mainJar->quests()->inRandomOrder()->first(); //Napi valtozas a questben

        $jars = Jar::whereNot('type', 'official_jar')
        ->whereHas('quests')
        ->with('quests')
        ->get();
        return view('home', compact('mainJar', 'mainQuest', 'jars'));
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
        $jar = Jar::create($request->validated());
        //return redirect()->route('jars.show', $jar)->with('success', 'New jar was added successfully.');
        return ; //Todo a return redirect mehetne a User/show page re ami Profil page kent funkcioanalhatna
    }

    /**
     * Display the specified resource.
     */
    public function show(Jar $jar, $quest = null)
    {
        $quests = $jar->quests()->orderBy('id')->get();

        if($quests->isEmpty()){
            return view('jars.show', [
                'jar' => $jar,
                'currentQuest' => null,
                'previousQuest' => null,
                'nextQuest' => null,
            ]);
        }
        
        if ($quest) {
            $currentQuest = $quests->firstWhere('id', $quest);
        }
        else {
            $currentQuest = $quests->first();
        }

        $currentIndex = $quests->pluck('id')->search($currentQuest->id);

        $previousQuest = $currentIndex > 0 
            ? $quests[$currentIndex - 1] 
            : $quests->last();

        $nextQuest = $currentIndex < $quests->count() - 1
            ? $quests[$currentIndex + 1]
            : $quests->first();

        

        return view("jars.show", [
            'jar' => $jar,
            'currentQuest' => $currentQuest,
            'previousQuest' => $previousQuest,
            'nextQuest' => $nextQuest
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Jar $jar)
    {
        //
    }

    public function join(Jar $jar){
        $user = Auth::user();

        $jar->users()->syncWithoutDetaching([
            auth()->id() => ['streak' => '0']
        ]);

        return redirect()->route('jars.show', $jar->id)->with('success', 'You joined the jar');

    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJarRequest $request, Jar $jar)
    {
        $jar->update($request->all());
        return response()->json(["msg" => "New document was updated successfully"]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jar $jar)
    {
        $jar->delete();
        return response()->json(["msg" => "New document was deleted successfully"]);
    }
}
