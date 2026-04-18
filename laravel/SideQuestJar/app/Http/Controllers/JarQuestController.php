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
        $this->authorize('update', $jar);
        return view("jars.quests.create", compact('jar'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJarQuestRequest $request, Jar $jar)
    {
        $this->authorize('update', $jar);
        $quest = $jar->quests()->create($request->validated());

        return redirect()
            ->route('jars.show', $jar)
            ->with('success', 'Quest created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(JarQuest $jarQuest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit( Jar $jar, JarQuest $jarQuest)
    {
        $this->authorize('update', $jar); //otelem sincs megirtam e egyenlore a megfelelo engedejeket hozza

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
        $this->authorize('delete', $jarQuest);

        if($jarQuest->jar_id !== $jar->id){
            abort(404);
        }

        $jarQuest->delete();

        return redirect()->route('jars.show', $jar)->with('success', 'Quest deleted successfully.');
    }
}
