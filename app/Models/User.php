<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable , HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'phone',
        'password',
        'location',
        'latitude',
        'longitude',
        'image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'email_verified_at',
        'location',
        'verification_code',
        'latitude',
        'longitude',
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
    public function generateVerificationCode()
    {
        $this->verification_code = rand(100000, 999999); // 6-digit code
        $this->save();
    }
    public function userLocations()
    {
        return $this->hasMany(UserLocation::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function favorites()
{
    return $this->hasMany(Favorite::class, 'user_id');
}

public function favoriteCarModels()
{
    return $this->belongsToMany(CarModel::class, 'favorites');
}

public function hasFavorited(CarModel $carModel)
{
    return $this->favorites()->where('car_model_id', $carModel->id)->exists();
}

}
