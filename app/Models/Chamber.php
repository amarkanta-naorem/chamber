<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chamber extends Model
{
    use HasFactory;
    protected $table = 'chambers';
    protected $fillable = ['sys_service_id', 'date', 'time', 'gps_time', 'tel_temperature'];

}
