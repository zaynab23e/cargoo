<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarModel extends Model
{
    protected $table = 'carmodels';

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

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
        'type_id'
    ];

    // علاقات Eager Loading افتراضية
    protected $with = [
        'cars',
        'modelName.type.brand',
        'images',
        'ratings.user'
    ];

    // اسم الموديل
    public function modelName()
    {
        return $this->belongsTo(ModelName::class, 'model_name_id');
    }

    // صور الموديل
    public function images()
    {
        return $this->hasMany(CarModelImage::class, 'car_model_id');
    }

    // التقييمات
    public function ratings()
    {
        return $this->hasMany(CarModelRating::class, 'car_model_id');
    }

    // متوسط التقييم
    public function avgRating()
    {
        return $this->ratings()->avg('rating');
    }

    // السيارات المرتبطة بالموديل
    public function cars()
    {
        return $this->hasMany(Car::class, 'carmodel_id');
    }

    // الموديلات المفضلة عند المستخدمين
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites', 'car_model_id', 'user_id');
    }

    // نوع الموديل (براند تايب)
    public function type()
    {
        return $this->belongsTo(BrandType::class, 'type_id'); 
    }
}
