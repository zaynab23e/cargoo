<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CarModel;
use App\Models\Type;

class ModelName extends Model
{
    protected $table = 'model_names';
    protected $hidden = ['created_at', 'updated_at'];

    protected $fillable = [
        'name', // مثال: 'Corolla'
        'type_id',
    ];

    // علاقة ModelName بـ Type
    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    // علاقة ModelName بـ CarModel (كل موديل اسم ممكن يكون له موديلات سيارات متعددة)
    public function carModels()
    {
        return $this->hasMany(CarModel::class, 'model_name_id');
    }

    // لو كل ModelName مرتبط بسيارة واحدة فقط
    public function carModel()
    {
        return $this->hasOne(CarModel::class, 'model_name_id');
    }
}
