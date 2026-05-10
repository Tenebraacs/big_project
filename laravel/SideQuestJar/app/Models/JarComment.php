<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JarComment extends Model
{
    protected $fillable = ['jar_id', 'user_id', 'content'];

    public function jar()
    {
        return $this->belongsTo(Jar::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
