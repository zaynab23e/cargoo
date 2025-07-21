<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
  protected $fillable = ['name','logo'];
  protected $hidden = ['created_at', 'updated_at'];

  public function types() 
  {
    return $this->hasMany(Type::class);

  }



}
