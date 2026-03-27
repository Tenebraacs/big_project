<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Like extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use SoftDeletes;
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function quest(){
        return $this->belongsTo(Quest::class);
    }

    public function questCompletion(){
        return $this->belongsTo(QuestCompletion::class);
    }

    public function jar(){
        return $this->belongsTo(Jar::class);
    }
}
