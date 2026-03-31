<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Friend extends Model
{
    /** @use HasFactory<\Database\Factories\FriendFactory> */
    use HasFactory;
    use SoftDeletes;
}
