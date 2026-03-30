<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JarQuest extends Model
{
    /** @use HasFactory<\Database\Factories\JarQuestFactory> */
    use HasFactory;



    protected $fillable = [
        'jar_id',
        'title',
        'picture',
        'description',
        'likes',
        'created_by'
    ];



    public function jar(){
        return $this->belongsTo(Jar::class);
    }

    public function user(){
        return $this->belongsTo(User::class, 'created_by');
    }

}




