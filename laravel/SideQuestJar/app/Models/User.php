<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;

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
        'profile_picture',
        'is_admin',
    ];


    public function getProfilePictureUrlAttribute(): string
    {
        $value = $this->profile_picture;

        if (!$value) {
            return 'https://i.ibb.co/Ngk3xx10/Godly-Pfp.png';
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        return asset('storage/' . $value);
    }

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

    public function quest_completions(){
        return $this->hasMany(QuestCompletion::class);
    }


    public function achievements(){
        return $this->belongsTo(Achievement::class);
    }

    public function customizations(){
        return $this->belongsToMany(Customization::class, 'user_customs');
        //Many to many connection to the users table via the user_customizations pivot table
    }

    public function likes(){
        return $this->hasMany(Like::class);
    }

    //Todo JarUsers Connection type (plusz nem helyes a tabla meg)


   public function createdJars()
    {
        return $this->hasMany(Jar::class, 'created_by');
    }

    public function jars()
    {
        return $this->belongsToMany(Jar::class, 'jar_users')
            ->withPivot('streak')
            ->withTimestamps();
    }
        


    


    public function friends()
    {
        return $this->belongsToMany(User::class, 'friends', 'user_id', 'friend_id')
            ->withPivot('status')
            ->wherePivot('status', 'accepted');
    }

    public function friendRequestsSent()
    {
        return $this->hasMany(Friend::class, 'user_id')->where('status', 'pending');
    }

    public function friendRequestsReceived()
    {
        return $this->hasMany(Friend::class, 'friend_id')->where('status', 'pending');
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }
}
