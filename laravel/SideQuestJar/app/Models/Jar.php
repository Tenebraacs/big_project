<?php

namespace App\Models;

use App\Observers\JarObserver;
use App\Policies\JarPolicy;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[ObservedBy(JarObserver::class)]
#[UsePolicy(JarPolicy::class)]
class Jar extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'picture',
        'type',
        'created_by',
        'current_daily_quest_id',
        'previous_daily_quest_id',
        'current_daily_quest_date',
    ];

    protected $appends = ['picture_url'];

    protected $casts = [
        'current_daily_quest_date' => 'date',
    ];

    public function getPictureUrlAttribute()
    {
        if (!$this->picture || $this->picture === 'https://i.ibb.co/2YvZypwJ/Jar-Quest-Placeholder.png' || $this->picture === 'images/default-jar.png') {
            return asset('images/default-jar.png');
        }

        if (Str::startsWith($this->picture, ['http://', 'https://'])) {
            return $this->picture;
        }

        return asset('storage/' . $this->picture);
    }

    public function quests()
    {
        return $this->hasMany(JarQuest::class);
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function jarUser()
    {
        return $this->hasMany(JarUser::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'jar_users')
            ->withPivot('streak')
            ->withTimestamps();
    }

    public function currentDailyQuest()
    {
        return $this->belongsTo(JarQuest::class, 'current_daily_quest_id');
    }

    public function previousDailyQuest()
    {
        return $this->belongsTo(JarQuest::class, 'previous_daily_quest_id');
    }

    public function comments()
    {
        return $this->hasMany(JarComment::class);
    }
}