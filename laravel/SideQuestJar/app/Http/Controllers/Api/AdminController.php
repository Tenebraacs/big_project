<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()?->is_admin, 403, 'Unauthorized action.');

        $search = $request->input('search');

        $users = User::query()
            ->withCount(['jars', 'createdJars', 'likes', 'questCompletions'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('fullname', 'like', '%' . $search . '%');
                });
            })
            ->orderByDesc('is_admin')
            ->orderBy('username')
            ->paginate(20);

        // Map users to append profile_picture_url so MAUI can easily use it
        $users->getCollection()->transform(function ($user) {
            $user->append('profile_picture_url');
            return $user;
        });

        return response()->json([
            'data' => $users->items(),
            'current_page' => $users->currentPage(),
            'last_page' => $users->lastPage(),
            'total' => $users->total()
        ]);
    }

    public function toggleAdmin($id)
    {
        abort_unless(auth()->user()?->is_admin, 403, 'Unauthorized action.');

        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'Cannot modify your own admin status'], 400);
        }

        $user->update([
            'is_admin' => ! $user->is_admin,
        ]);

        return response()->json([
            'message' => 'Admin status updated successfully',
            'is_admin' => $user->is_admin
        ]);
    }

    public function deleteUser($id)
    {
        abort_unless(auth()->user()?->is_admin, 403, 'Unauthorized action.');

        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'Cannot delete yourself'], 400);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }
}
