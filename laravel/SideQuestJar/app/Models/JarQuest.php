<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class JarQuest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'picture',
        'description',
        'jar_id',
        'created_by',
    ];

    protected $appends = ['picture_url'];

    public function getPictureUrlAttribute()
    {
        if (!$this->picture || $this->picture === 'https://i.ibb.co/2YvZypwJ/Jar-Quest-Placeholder.png' || $this->picture === 'images/default-jar.png') {
            return $this->jar ? $this->jar->picture_url : asset('images/default-jar.png');
        }

        if (Str::startsWith($this->picture, ['http://', 'https://'])) {
            return $this->picture;
        }

        return asset('storage/' . $this->picture);
    }

    public function jar()
    {
        return $this->belongsTo(Jar::class);
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function completions()
    {
        return $this->hasMany(QuestCompletion::class);
    }
}