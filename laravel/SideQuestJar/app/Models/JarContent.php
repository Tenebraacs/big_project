<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JarContent extends Model
{
    /** @use HasFactory<\Database\Factories\JarContentFactory> */
    use HasFactory, SoftDeletes;

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
