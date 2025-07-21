<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverLocation extends Model
{
protected $fillable = ['driver_id','location','latitude','longitude'];
}
