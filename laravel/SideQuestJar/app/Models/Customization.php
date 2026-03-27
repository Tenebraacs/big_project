<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customization extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use SoftDeletes;
    public function users(){
        return $this->belongsToMany(User::class, 'user_customs');
        //Many to many connection to the customizations table via the user_customizations pivot table
    }
}
