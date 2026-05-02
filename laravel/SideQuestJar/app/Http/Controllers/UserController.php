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

        return view('users.index');
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
    public function show(string $username)
    {
        $user = User::query()
            ->where('username', $username)
            ->firstOrFail();

        // If your profile page needs jars like your own profile page does:
        // Adjust these queries to match your actual relationships/DB schema.

        $myJars = $user->jars ?? collect();          // replace with real relation/query
        $joinedJars = $user->joinedJars ?? collect(); // replace with real relation/query

        return view('users.show', [
            'user' => $user,
            'myJars' => $myJars,
            'joinedJars' => $joinedJars,
        ]);
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

        return view('users.profile', [
            'user' => $user,

            'myJars' => $user->jars()->with('user')->latest()->get(),

            'joinedJars' => $user->jars()->with('user')->latest()->get(),
        ]);
    }
}
