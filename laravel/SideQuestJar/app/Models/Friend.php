<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Friend extends Model
{
    /** @use HasFactory<\Database\Factories\FriendFactory> */
    use HasFactory;
    
    // We don't use softdeletes in the updated migration, so I'll remove it.
    // Also, setting fillable and relationships.
    protected $fillable = ['user_id', 'friend_id', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function friend()
    {
        return $this->belongsTo(User::class, 'friend_id');
    }
}
