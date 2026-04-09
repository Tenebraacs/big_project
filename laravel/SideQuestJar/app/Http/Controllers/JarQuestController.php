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
        $this->authorize('create', JarQuest::class);
        return view("jars.quests.create", compact('jar'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJarQuestRequest $request, Jar $jar)
    {
        $this->authorize('create', JarQuest::class);
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
    public function edit(JarQuest $jarQuest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJarQuestRequest $request, JarQuest $jarQuest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JarQuest $jarQuest)
    {
        //
    }
}
