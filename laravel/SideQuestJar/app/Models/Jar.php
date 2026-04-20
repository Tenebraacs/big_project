<?php

namespace App\Models;

use App\Observers\JarObserver;
use App\Policies\JarPolicy;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
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
        'description',
        'picture',
        'type',
        'created_by'
    ];

    protected $appends = ['picture_url'];

    public function getPictureUrlAttribute()
    {
        if (!$this->picture){
            return 'https://i.ibb.co/2YvZypwJ/Jar-Quest-Placeholder.png';
        }

        
        if(Str::startsWith($this->picture, ['http://', 'https://'])) {
            return $this->picture;
        }

        return asset('storage/' . $this->picture);


    }
    


    public function quests(){
        return $this->hasMany(JarQuest::class);
    }

    public function likes(){
        return $this->morphMany(Like::class, 'likeable');
    }

    public function jar_user(){
        return $this->hasMany(JarUser::class);
    }



    public function user(){
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users(){
        return $this->belongsToMany(User::class, 'jar_users')
            ->withPivot('streak')
            ->withTimestamps();
    }







}
