<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\QuestCompletion;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function register(Request $request){
        $validated = $request->validate([
            'username' => ['required', 'string', 'unique:users,username'],
            'email' => ['required', 'email', 'unique:users,email'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date', 'before_or_equal:'.now()->subYear(16)->format('Y-m-d')],
            'password' => ['required', 'string', 'confirmed',
            Password::min(12)->letters()->mixedCase()->numbers()->symbols()->uncompromised()]
        ]);

        $user = User::create([
            'username' => $validated["username"],
            'fullname' => $validated['first_name']. ' '. $validated['last_name'],
            'email' => $validated["email"],
            'birth_date' => $validated["birth_date"],
            'password' => Hash::make($validated["password"])
        ]);

        $token = $user->createToken($request->userAgent(), ['*'], now()->addWeek())->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully.',
            'user' => $user,
            'token' => $token
        ], 201);
    }


    public function login(Request $request){
        $request->validate([
            "email" => ["required", "email"],
            "password" => ["required", "string"],
            "device_id" => ["string"]     
            ]          
        );

        $credentials = [
            "email" => $request->email,
            "password" => $request->password
        ];

        if(Auth::attempt($credentials) === false){
            return response()->json(["msg" => "Invalid Credentials"], 418);
        }

        $user = auth()->user();

        $existingToken = $user->tokens()->where('name', $request->device_id . "|" . $request->userAgent())->first();

        if($existingToken){
            $existingToken->delete();
        }

        $token = $user->createToken($request->device_id."|".$request->userAgent(), ['*'], now()->addWeek())->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }


    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json(["msg" => "Logout succesful"]);
    }

    public function user(Request $request){
        return $request->user();   
    }

    public function show(Request $request)
    {
        return response()->json($request->user());
    }

    public function navbarData(Request $request)
    {
        $user = $request->user();

        $dailyJar = $user->jars()
            ->where('type', 'official_jar')
            ->where('name', "Today's Adventure")
            ->first();

        $streak = $dailyJar?->pivot?->streak ?? 0;

        $startOfWeek = Carbon::today()->startOfWeek(Carbon::MONDAY);
        $endOfToday = Carbon::today()->endOfDay();

        $completions = QuestCompletion::where('user_id', $user->id)
            ->whereBetween('completion_date', [$startOfWeek, $endOfToday])
            ->whereHas('jarQuest', function ($query) {
                $query->whereHas('jar', function ($jarQuery) {
                    $jarQuery->where('type', 'official_jar')
                        ->where('name', "Today's Adventure");
                });
            })
            ->get();

        $filledIndexes = $completions
            ->map(function ($completion) {
                return Carbon::parse($completion->completion_date)->dayOfWeekIso - 1;
            })
            ->unique()
            ->values()
            ->all();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'profile_picture_url' => $user->profile_picture_url,
                'is_admin' => (bool) $user->is_admin,
                'user_xp' => $user->user_xp ?? 0
            ],
            'streak' => $streak,
            'filled_indexes' => $filledIndexes,
            'today_index' => now()->dayOfWeekIso - 1
        ]);
    }

    /**
     * GET /api/users/search?query=...
     * Returns up to 5 users matching the username query (used by friends autocomplete).
     */
    public function search(Request $request)
    {
        $query = $request->get('query');
        if (!$query) return response()->json([]);

        $users = \App\Models\User::where('username', 'LIKE', "%{$query}%")
            ->where('id', '!=', $request->user()->id)
            ->limit(5)
            ->get(['id', 'username', 'profile_picture']);

        $users->each(fn($user) => $user->append('profile_picture_url'));

        return response()->json($users);
    }


}
