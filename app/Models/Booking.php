<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
  protected $fillable = ['carmodel_id','car_id','user_id','location_id','driver_id','start_date','end_date','final_price','status','payment_method','payment_status','transaction_id','additional_driver']; 

  protected $hidden = ['created_at','updated_at'];
  public function user()
  {
    return $this->belongsTo(User::class);
  }
  public function car()
  {
    return $this->belongsTo(Car::class);
  }
  public function carModel()
  {
      return $this->belongsTo(CarModel::class, 'carmodel_id');
  }
  public function location()
  {
    return $this->belongsTo(UserLocation::class, 'location_id');
  }

  public function driver()
  {
    return $this->belongsTo(Driver::class);
  }

}
