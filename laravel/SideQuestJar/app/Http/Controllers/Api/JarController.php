<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJarRequest;
use App\Models\Jar;
use Illuminate\Http\Request;

class JarController extends Controller
{
    public function index(){
        $jar = Jar::all();
        return response()->json(["jar" => $jar]);
    }

    public function store(StoreJarRequest $request) {
        $jar = Jar::create($request->all());
        return response()->json(["msg" => "Jar added", $jar]);
    }
}
