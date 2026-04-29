<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_unless(auth()->user()?->is_admin, 403);

        return view('user.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function user()
    {
        $user = auth()->user();

        return view('user.show', [
            'user' => $user,

            'myJars' => $user->jars()->with('user')->latest()->get(),

            'joinedJars' => $user->jars()->with('user')->latest()->get(),
        ]);
    }
}
