<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quest extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use SoftDeletes;
    public function jar(){
        return $this->hasOne(Jar::class);
    }

    public function quest_completion(){
        return $this->belongsTo(QuestCompletion::class);
    }

    public function like(){
        return $this->belongsTo(Like::class);
    }
}

