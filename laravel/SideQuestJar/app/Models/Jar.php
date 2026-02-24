<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jar extends Model
{
    /** @use HasFactory<\Database\Factories\JarFactory> */
    use HasFactory, SoftDeletes;

    public function quest(){
        return $this->belongsTo(Quest::class);
    }

    public function like(){
        return $this->belongsTo(Like::class);
    }

    public function jar_user(){
        return $this->belongsTo(JarUser::class);
    }

    public function users(){
        return $this->hasMany(User::class);
    }
}
