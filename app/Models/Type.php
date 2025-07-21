<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    protected $fillable = ['name','description','brand_id'];
    protected $hidden = ['created_at', 'updated_at'];

    
    public function brand() 
    {
        return $this->belongsTo(Brand::class);

    }
    public function modelNames() 
    {
        return $this->hasMany(ModelName::class);

    }
}
