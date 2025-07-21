<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
  protected $fillable = ['carmodel_id','plate_number','status','image','color'];
  protected $hidden = ['created_at', 'updated_at'];

  
  public function carModel()
  {
    return $this->belongsTo(CarModel::class, 'carmodel_id');
  }
  public function bookings()
  {
    return $this->hasMany(Booking::class);
  }

public function images()
{
    return $this->hasMany(Image::class);
}


}
