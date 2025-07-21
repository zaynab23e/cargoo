<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarModelImage extends Model
{
    protected $fillable = ['car_model_id','image','is_main'];
    
    public function carModel()
{
    return $this->belongsTo(CarModel::class);
}
}
