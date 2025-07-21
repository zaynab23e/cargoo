<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = ['user_id', 'car_model_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function carModel()
    {
        return $this->belongsTo(CarModel::class);
    }

    public function favoritedBy()
{
    return $this->belongsToMany(User::class, 'favorites');
}

    
}
