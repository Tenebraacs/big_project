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

    public function store(StoreJarRequest $request) {
        $jar = Jar::create($request->all());
        return response()->json(["msg" => "Jar added", $jar]);
    }

    public function update(UpdateJarRequest $request, Jar $jar){
        $jar->update($request->all());
        return response()->json(["msg" => "Jar update", $jar]);
    }

    public function destroy(Jar $jar){
        $jar->delete();
        return response()->json(["msg" => "Jar was deleted"]);
    }

    public function discover() 
    {
        $mainJar = Jar::where('type', 'official_jar')->first();
        $mainQuest = $mainJar ? $mainJar->quests()->inRandomOrder()->first() : null;

        $popularJars = Jar::where('type', '!=', 'official_jar')
                            ->orderBy('likes', 'desc')
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




}
