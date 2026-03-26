<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
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

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully.',
            'user' => $user,
            'token' => $token
        ], 201);
    }
}
