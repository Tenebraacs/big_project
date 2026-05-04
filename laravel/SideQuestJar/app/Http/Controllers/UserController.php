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

        $viewer = auth()->user();
        $isFriend = $viewer ? $viewer->friends()->where('users.id', $user->id)->exists() : false;

        $filterJars = function($query) use ($viewer, $isFriend, $user) {
            if (!$viewer) {
                return $query->where('type', 'public_jar');
            }

            return $query->where(function($q) use ($viewer, $isFriend, $user) {
                $q->where('type', 'public_jar')
                  ->orWhere(function($subQ) use ($isFriend, $user, $viewer) {
                      $subQ->where('type', 'friends_only_jar')
                           ->where(function($fq) use ($isFriend, $user, $viewer) {
                               if ($isFriend) {
                                   $fq->where('created_by', $user->id);
                               }
                               // if the viewer is the creator, they can see it anyway
                               $fq->orWhere('created_by', $viewer->id);
                           });
                  })
                  ->orWhere(function($subQ) use ($viewer) {
                      $subQ->where('type', 'invite_only_jar')
                           ->whereHas('users', function($userQ) use ($viewer) {
                               $userQ->where('users.id', $viewer->id);
                           });
                  });
            });
        };

        $myJars = $filterJars($user->createdJars())->latest()->get();
        $joinedJars = $filterJars($user->jars()->where('created_by', '!=', $user->id))->latest()->get();

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

    public function search(Request $request)
    {
        $query = $request->get('query');
        if (!$query) return response()->json([]);

        $users = User::where('username', 'LIKE', "%{$query}%")
            ->where('id', '!=', auth()->id())
            ->limit(5)
            ->get(['username', 'profile_picture']);

        // Add profile_picture_url to each user
        $users->each(function($user) {
            $user->append('profile_picture_url');
        });

        return response()->json($users);
    }
}
