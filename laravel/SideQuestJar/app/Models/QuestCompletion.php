<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestCompletion extends Model
{
    public function quest(){
        return $this->hasOne(Quest::class);
    }

    public function user(){
        return $this->hasOne(User::class);
    }
}
