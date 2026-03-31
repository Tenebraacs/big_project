<?php

namespace App\Models;

use App\Observers\JarObserver;
use App\Policies\JarPolicy;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

#[ObservedBy(JarObserver::class)]
#[UsePolicy(JarPolicy::class)]
class Jar extends Model
{
    /** @use HasFactory<\Database\Factories\JarFactory> */
    use HasFactory, SoftDeletes;


    protected $fillable = [
        'name',
        'likes',
        'type',
        'created_by'
    ];


    


    public function quests(){
        return $this->hasMany(JarQuest::class);
    }

    public function like(){
        return $this->belongsTo(Like::class);
    }

    public function jar_user(){
        return $this->hasMany(JarUser::class);
    }



    public function user(){
        return $this->belongsTo(User::class, 'created_by');
    }







}
