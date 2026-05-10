<?php

namespace App\Http\Controllers;

use App\Models\Jar;
use App\Models\JarInvitation;
use App\Models\User;
use Illuminate\Http\Request;

class JarInvitationController extends Controller
{
    /**
     * Send an invite to a user for a given jar.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jar_id'     => 'required|exists:jars,id',
            'invitee_id' => 'required|exists:users,id',
        ]);

        $jar     = Jar::findOrFail($request->jar_id);
        $invitee = User::findOrFail($request->invitee_id);
        $auth    = auth()->user();

        // Must be a member or creator of the jar
        $isMember  = $jar->users()->where('users.id', $auth->id)->exists();
        $isCreator = $jar->created_by === $auth->id;
        if (!$isMember && !$isCreator) {
            return response()->json(['error' => 'You are not a member of this jar.'], 403);
        }

        // Invitee must not already be in the jar
        if ($jar->users()->where('users.id', $invitee->id)->exists() || $jar->created_by === $invitee->id) {
            return response()->json(['error' => 'User is already in this jar.'], 422);
        }

        // Create or update the invitation (allow re-invite if previously declined)
        $invitation = JarInvitation::updateOrCreate(
            ['jar_id' => $jar->id, 'invitee_id' => $invitee->id],
            ['inviter_id' => $auth->id, 'status' => 'pending']
        );

        return response()->json(['message' => 'Invitation sent!', 'invitation' => $invitation]);
    }

    /**
     * Accept a pending invitation.
     */
    public function accept(JarInvitation $jarInvitation)
    {
        abort_unless(auth()->id() === $jarInvitation->invitee_id, 403);
        abort_unless($jarInvitation->status === 'pending', 422);

        $jarInvitation->update(['status' => 'accepted']);

        // Add the user to the jar
        $jarInvitation->jar->users()->syncWithoutDetaching([auth()->id()]);

        return redirect()->route('jars.show', $jarInvitation->jar)
            ->with('success', 'You joined the jar!');
    }

    /**
     * Decline a pending invitation.
     */
    public function decline(JarInvitation $jarInvitation)
    {
        abort_unless(auth()->id() === $jarInvitation->invitee_id, 403);
        abort_unless($jarInvitation->status === 'pending', 422);

        $jarInvitation->update(['status' => 'declined']);

        return back()->with('success', 'Invitation declined.');
    }

    /**
     * Return pending invitations for the authenticated user (for the notification badge).
     */
    public function pending()
    {
        $invitations = JarInvitation::with(['jar', 'inviter'])
            ->where('invitee_id', auth()->id())
            ->where('status', 'pending')
            ->get();

        return response()->json($invitations);
    }
}
