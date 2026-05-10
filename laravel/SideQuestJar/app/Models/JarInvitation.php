<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JarInvitation extends Model
{
    protected $fillable = ['jar_id', 'inviter_id', 'invitee_id', 'status'];

    public function jar()
    {
        return $this->belongsTo(Jar::class);
    }

    public function inviter()
    {
        return $this->belongsTo(User::class, 'inviter_id');
    }

    public function invitee()
    {
        return $this->belongsTo(User::class, 'invitee_id');
    }
}
