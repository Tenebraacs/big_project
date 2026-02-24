<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JarUser extends Model
{
    public function user(){
        return $this->hasOne(User::class);
    }

    public function jars(){
        return $this->hasMany(Jar::class);
    }
}
