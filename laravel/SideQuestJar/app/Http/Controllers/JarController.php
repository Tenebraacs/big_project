<?php

namespace App\Http\Controllers;

use App\Models\Jar;
use App\Http\Requests\StoreJarRequest;
use App\Http\Requests\UpdateJarRequest;
use Illuminate\Http\Request;


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

    public function mainJar(){
        //Lekeri az elso type == official_jar adatait
        $jar = Jar::where('type', 'official_jar')->first();
        $content = $jar->contents()->inRandomOrder()->first();
        return view('home', compact('jar', 'content'));
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
        $jar = Jar::create($request->all());
        return response()->json(["msg" => "New Jar was added successfully", "jar" => $jar]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Jar $jar)
    {
        return response()->json(["jar" => $jar]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Jar $jar)
    {
        //
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
