<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Str;

class UserProfileController extends Controller
{
    public function profile(Request $request)
    {
        $user = $request->user();

        // Jars the user has created
        $myJars = $user->createdJars()->with('user')->latest()->get();

        // Jars the user has joined but not created
        $joinedJars = $user->jars()->where('created_by', '!=', $user->id)->with('user')->latest()->get();

        return response()->json([
            'user' => $user,
            'my_jars' => $myJars,
            'joined_jars' => $joinedJars
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'username' => ['nullable', 'string', 'min:3', 'max:30', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'profile_picture' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        if ($request->has('username')) {
            $user->username = $validated['username'];
        }

        if ($request->has('email')) {
            $user->email = $validated['email'];
        }

        if ($request->hasFile('profile_picture')) {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($request->file('profile_picture')->getRealPath());
            $image->cover(400, 400);

            $filename = 'profile-pictures/' . Str::random(40) . '.webp';
            Storage::disk('public')->put($filename, (string) $image->toWebp(80));

            $user->profile_picture = $filename;
        }

        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user' => $user->fresh()
        ]);
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()],
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json(['message' => 'Current password is incorrect.'], 400);
        }

        $user->password = Hash::make($validated['new_password']);
        $user->save();

        return response()->json([
            'message' => 'Password updated successfully.'
        ]);
    }
}
