<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\HasUuid;
use Laravel\Passport\HasApiTokens;
use App\Notifications\CustomResetPassword;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable,HasRoles,HasApiTokens,HasUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = [];


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

    // protected static function booted()
    // {
    //     static::addGlobalScope('excludeAdmin', function (Builder $builder) {
    //         $builder->where('name', '!=', 'admin')
    //                 ->where('email', '!=', 'admin@gmail.com');
    //     });
    // }
    public function scopeExcludeAdmin($query)
    {
        return $query->where('email', '!=', 'admin@gmail.com');
    }

    // Define a local scope for active roles
    public function scopeActive(Builder $query)
    {
        return $query->where('status', 'Active');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function role()
    {
        // return $this->belongsTo(Role::class, 'role_uuid', 'uuid');
        // return $this->hasOne(Role::class, 'uuid', 'role_uuid')->withoutGlobalScope('exclude_super_admin');
        return $this->hasOne(Role::class, 'uuid', 'role_uuid');
    }
    
    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format('d-m-Y');
    }

    //Custom Password Reset Notification
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token));
    }

    public function fcmTokens()
    {
        return $this->hasMany(UserFcmToken::class,'user_uuid','uuid');
    }
}
