<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarModel extends Model
{
    protected $table = 'carmodels';
    protected $hidden = ['created_at', 'updated_at'];


    protected $fillable = [
        'year',
        'count', 
        'price', 
        'image',
        'model_name_id',
        'engine_type',
        'transmission_type',
        'seat_type',
        'seats_count',
        'acceleration',
    ];

    public function modelName()
    {
        return $this->belongsTo(ModelName::class, 'model_name_id');
    }
    public function images()
    {
        return $this->hasMany(CarModelImage::class);
    }
    public function ratings()
    {
        return $this->hasMany(CarModelRating::class, 'car_model_id');
    }
    public function avgRating()
    {
        return $this->ratings()->avg('rating');
    }

    public function cars()
    {
        return $this->hasMany(Car::class, 'carmodel_id');
    }

    
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites');
    }
    public function type()
    {
        return $this->belongsTo(BrandType::class, 'type_id'); 
    }


}
