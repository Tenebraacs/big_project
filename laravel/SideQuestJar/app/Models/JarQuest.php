<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JarQuest extends Model
{
    /** @use HasFactory<\Database\Factories\JarQuestFactory> */
    use HasFactory;
    use SoftDeletes;



    protected $fillable = [
        
        'title',
        'picture',
        'description',
        
    ];



    public function jar(){
        return $this->belongsTo(Jar::class);
    }

    public function user(){
        return $this->belongsTo(User::class, 'created_by');
    }

}




