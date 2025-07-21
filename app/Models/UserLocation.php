<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLocation extends Model
{
    protected $fillable = ['user_id','location','latitude','longitude','is_active'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    protected $hidden = ['created_at', 'updated_at'];
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'location_id');
    }

}
