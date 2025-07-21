<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModelName extends Model
{
    protected $table = 'model_names';
    protected $hidden = ['created_at', 'updated_at'];

    protected $fillable = [
        'name', // e.g., 'Corolla'
        'type_id',
    ];

    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function carModels()
    {
        return $this->hasMany(CarModel::class, 'model_name_id');
    }
}
