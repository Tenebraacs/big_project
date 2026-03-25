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
        $jar = Jar::all();
        return response()->json(["jar" => $jar, "msg" => "Here are your books"]);
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




}
