<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class QuestCompletion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'completion_date',
        'picture',
        'description',
        'type',
        'user_id',
        'jar_quest_id',
    ];

    protected $appends = ['picture_url'];

    public function getPictureUrlAttribute()
    {
        if (!$this->picture) {
            return 'https://i.ibb.co/2YvZypwJ/Jar-Quest-Placeholder.png';
        }

        if (Str::startsWith($this->picture, ['http://', 'https://'])) {
            return $this->picture;
        }

        return asset('storage/' . $this->picture);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jarQuest()
    {
        return $this->belongsTo(JarQuest::class);
    }
}