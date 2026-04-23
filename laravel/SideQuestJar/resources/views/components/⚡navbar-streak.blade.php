<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\QuestCompletion;
use Carbon\Carbon;

new class extends Component
{
    protected $listeners = ['streak-updated' => '$refresh'];

    public function getStreakProperty()
    {
        if (!Auth::check()) {
            return 0;
        }

        $user = Auth::user();

        $dailyJar = $user->jars()
            ->where('type', 'official_jar')
            ->where('name', "Today's Adventure")
            ->first();

        return $dailyJar?->pivot?->streak ?? 0;
    }

    public function getTodayIndexProperty()
    {
        return now()->dayOfWeekIso - 1; // Monday = 0, Sunday = 6
    }

    public function getFilledIndexesProperty()
    {
        if (!Auth::check()) {
            return [];
        }

        $user = Auth::user();

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

        return $completions
            ->map(function ($completion) {
                return Carbon::parse($completion->completion_date)->dayOfWeekIso - 1;
            })
            ->unique()
            ->values()
            ->all();
    }

    public function getTodayDoneProperty()
    {
        return in_array($this->todayIndex, $this->filledIndexes, true);
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
                        <span class="streak-dot {{ in_array($index, $this->filledIndexes, true) ? 'filled' : '' }}"></span>
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