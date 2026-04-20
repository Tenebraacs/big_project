<?php

namespace App\Http\Controllers;

use App\Models\Jar;
use App\Models\JarQuest;
use App\Http\Requests\StoreJarQuestRequest;
use App\Http\Requests\UpdateJarQuestRequest;

class JarQuestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Jar $jar)
    {
        $this->authorize('update', $jar); //A Jar engedelyeit nezi meg nem a jarQuest-et Ha tovabb fejlesztunk lehet rola szo hogy ne csak a jar keszito tudjon questet hozza adni
        return view("jars.quests.create", compact('jar'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJarQuestRequest $request, Jar $jar)
    {
        $this->authorize('update', $jar);

        $validated = $request->validated();

        if ($request->hasFile('picture')) {
            $validated['picture'] = $request->file('picture')->store('quests', 'public');

        }

        $quest = $jar->quests()->create($validated);

        return redirect()
            ->route('jars.show', [
                'jar' => $jar->id,
                'quest' => $quest->id,
            ])
            ->with('success', 'Quest created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit( Jar $jar, JarQuest $jarQuest)
    {
        $this->authorize('update', $jar); 

        if($jarQuest->jar_id !== $jar->id){
            abort(404);
        }

        return view('jars.quests.edit' , compact('jar', 'jarQuest'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJarQuestRequest $request, Jar $jar, JarQuest $jarQuest)
    {
        $this->authorize('update', $jar);

        if($jarQuest->jar_id !== $jar->id){
            abort(404);
        }

        $jarQuest->update($request->validated());

        return redirect()
            ->route('jars.show', [
                'jar' => $jar->id,
                'quest' => $jarQuest->id,
            ])->with('success', 'Quest updated successfully.');


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jar $jar, JarQuest $jarQuest)
    {
        $this->authorize('update', $jar); 

        if($jarQuest->jar_id !== $jar->id){
            abort(404);
        }

        $jarQuest->delete();

        return redirect()->route('jars.show', $jar)->with('success', 'Quest deleted successfully.');
    }
}
