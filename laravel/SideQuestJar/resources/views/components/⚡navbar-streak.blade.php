<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

new class extends Component
{
    protected $listeners = ['streak-updated' => '$refresh'];

    public function getStreakProperty()
    {
        if (!Auth::check()) {
            return 0;
        }

        $user = User::find(Auth::id());

        if (!$user) {
            return 0;
        }

        $dailyJar = $user->jars()
            ->where('type', 'official_jar')
            ->where('name', "Today's Adventure")
            ->first();

        return $dailyJar?->pivot?->streak ?? 0;
    }

    public function getCompletedDaysProperty()
    {
        return min($this->streak, 7);
    }

    public function getTodayIndexProperty()
    {
        return now()->dayOfWeekIso - 1;
    }

    public function getTodayDoneProperty()
    {
        return $this->todayIndex < $this->completedDays;
    }

    public function getTodayLetterProperty()
    {
        return ['M', 'T', 'W', 'T', 'F', 'S', 'S'][$this->todayIndex];
    }
};
?>

<div>
    <div class="mobile-menu-streak d-md-none">
        <div class="mobile-streak-pill rounded-pill">
            <div class="mobile-today-indicator">
                <span class="mobile-streak-dot {{ $this->todayDone ? 'filled' : '' }}"></span>
                <span class="mobile-today-label">Today ({{ $this->todayLetter }})</span>
            </div>

            <div class="mobile-streak-count">
                <i class="bi bi-fire"></i>
                <span class="mobile-streak-number">{{ $this->streak }}</span>
                <span class="mobile-streak-label">day streak</span>
            </div>
        </div>
    </div>

    <div class="nav-center d-none d-md-flex">
        <div class="streak-pill px-4 py-3 rounded-pill">
            <div class="streak-week">
                @foreach (['M','T','W','T','F','S','S'] as $index => $day)
                    <div class="streak-day">
                        <span class="streak-dot {{ $index < $this->completedDays ? 'filled' : '' }}"></span>
                        <small>{{ $day }}</small>
                    </div>
                @endforeach
            </div>

            <div class="streak-divider"></div>

            <div class="streak-count">
                <i class="bi bi-fire"></i>
                <span class="streak-number">{{ $this->streak }}</span>
                <span class="streak-label">day streak</span>
            </div>
        </div>
    </div>
</div>