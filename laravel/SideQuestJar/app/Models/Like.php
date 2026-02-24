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
        return $this->hasOne(User::class);
    }

    public function quest(){
        return $this->hasOne(Quest::class);
    }

    public function jar(){
        return $this->hasOne(Jar::class);
    }
}
