<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'fullname',
        'birth_date',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function quest_completion(){
        return $this->belongsTo(QuestCompletion::class);
    }

    public function user_role(){
        return $this->belongsTo(UserRoles::class);
    }

    public function achievements(){
        return $this->belongsToMany(Achievement::class);
    }

    public function customizations(){
        return $this->belongsToMany(Customization::class);
    }

    public function like(){
        return $this->belongsTo(Like::class);
    }

    public function jar_user(){
        return $this->belongsTo(JarUser::class);
    }

    public function jars(){
        return $this->hasMany(Jar::class);
    }
}
