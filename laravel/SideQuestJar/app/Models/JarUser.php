<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class JarUser extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    protected $fillable = [
        'jar_id',
        'user_id',
        'streak',
    ];



    public function user(){
        return $this->belongsTo(User::class);
    }

    public function jars(){
        return $this->belongsTo(Jar::class);
    }
}
