<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JarUser extends Model
{
    use HasFactory;

    protected $table = 'jar_users';

    protected $fillable = [
        'jar_id',
        'user_id',
        'streak',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jar()
    {
        return $this->belongsTo(Jar::class);
    }
}