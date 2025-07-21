<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrandType extends Model
{
    protected $table = 'brand_types';
      protected $fillable = ['brand_id','type_id']; 

}
