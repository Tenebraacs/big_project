<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quest extends Model
{
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

