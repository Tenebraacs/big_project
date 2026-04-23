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

<div class="mobile-streak-menu-card">
    <div class="mobile-streak-menu-top">
        <span class="mobile-streak-menu-title">
            Today ({{ $this->todayLetter }})
        </span>

        <div class="mobile-streak-menu-count">
            <i class="bi bi-fire"></i>
            <span>{{ $this->streak }} day streak</span>
        </div>
    </div>

    <div class="mobile-streak-menu-week">
        @foreach (['M','T','W','T','F','S','S'] as $index => $day)
            <div class="streak-day">
                <span class="
                    streak-day-button
                    {{ in_array($index, $this->filledIndexes, true) ? 'filled' : '' }}
                    {{ $index === $this->todayIndex ? 'today' : '' }}
                "></span>
                <small>{{ $day }}</small>
            </div>
        @endforeach
    </div>
</div>