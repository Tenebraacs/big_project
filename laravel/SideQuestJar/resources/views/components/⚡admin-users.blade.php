<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;

new class extends Component
{
    use WithPagination;
    
    protected string $paginationTheme = 'bootstrap';

    public string $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function toggleAdmin(int $userId)
    {
        abort_unless(auth()->user()?->is_admin, 403);

        $user = User::findOrFail($userId);

        if ($user->id === auth()->id()) {
            return;
        }

        $user->update([
            'is_admin' => ! $user->is_admin,
        ]);
    }

    public function deleteUser(int $userId)
    {
        abort_unless(auth()->user()?->is_admin, 403);

        $user = User::findOrFail($userId);

        if ($user->id === auth()->id()) {
            return;
        }

        $user->delete();
    }

    public function getUsersProperty()
    {
        return User::query()
            ->withCount(['jars', 'createdJars', 'likes', 'quest_completions'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('username', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('fullname', 'like', '%' . $this->search . '%');
                });
            })
            ->orderByDesc('is_admin')
            ->orderBy('username')
            ->paginate(20);
    }
};
?>

<div class="container-fluid">
    <div class="card bg-block-blue rounded-5 p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h1 class="jar-name fw-bold mb-1">Manage Users</h1>
                <p class="mb-0" style="color: #EAB2A0;">
                    {{ $this->users->total() }} users found
                </p>
            </div>

            <input
                type="text"
                wire:model.live="search"
                class="form-control claim-input"
                placeholder="Search username, email, full name..."
                style="max-width: 360px;"
            >
        </div>

        <div class="table-responsive admin-table-scroll">
            
            

            <table class="table align-middle admin-users-table mb-0">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Joined Jars</th>
                        <th>Created Jars</th>
                        <th>Likes</th>
                        <th>Completions</th>
                        <th>Role</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($this->users as $user)
                        <tr>
                            <td class="admin-user-cell">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="user-avatar">
                                        <img src="{{ $user->profile_picture_url }}" alt="{{ $user->username }}">
                                    </span>

                                    <strong>{{ $user->username }}</strong>

                                    @if ($user->id === auth()->id())
                                        <span class="badge text-bg-primary">You</span>
                                    @endif
                                </div>
                            </td>

                            <td class="admin-email-cell">{{ $user->email }}</td>
                            <td>{{ $user->jars_count }}</td>
                            <td>{{ $user->created_jars_count }}</td>
                            <td>{{ $user->likes_count }}</td>
                            <td>{{ $user->quest_completions_count }}</td>

                            <td>
                                @if ($user->is_admin)
                                    <span class="badge text-bg-dark">Admin</span>
                                @else
                                    <span class="badge text-bg-secondary">User</span>
                                @endif
                            </td>

                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <button
                                        type="button"
                                        wire:click="toggleAdmin({{ $user->id }})"
                                        class="btn btn-sm pink-button rounded-pill"
                                        @disabled($user->id === auth()->id())
                                    >
                                        {{ $user->is_admin ? 'Remove Admin' : 'Make Admin' }}
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="deleteUser({{ $user->id }})"
                                        wire:confirm="Delete this user?"
                                        class="btn btn-sm btn-outline-danger rounded-pill"
                                        @disabled($user->id === auth()->id())
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                No users found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="admin-pagination mt-3">
                {{ $this->users->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>
</div>