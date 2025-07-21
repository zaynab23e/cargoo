<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarModelRating extends Model
{
    protected $table = 'car_model_ratings';

    protected $fillable = [
        'user_id',
        'car_model_id',
        'rating',
        'review',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function carModel()
    {
        return $this->belongsTo(CarModel::class, 'car_model_id');
    }



}
