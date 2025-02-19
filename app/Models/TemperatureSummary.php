<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemperatureSummary extends Model
{
    use HasFactory;

    // Specify the table name if it doesn't follow Laravel's convention
    protected $table = 'temperature_summary';

    // Specify the primary key if it's not the default 'id'
    protected $primaryKey = 'id';

    // If the primary key is not an auto-incrementing integer
    public $incrementing = true;

    // If the primary key is not an integer
    protected $keyType = 'bigint';

    // Define the fillable properties (mass assignment)
    protected $fillable = [
        'sys_service_id',
        'min_temp',
        'max_temp',
        'avg_temp',
        'temp_date',
        'updatetime'
    ];

    // Optionally define hidden properties if you want to hide some attributes
    // protected $hidden = [];

    // Optionally define casts if you need to cast attributes to a specific type
    protected $casts = [
        'min_temp' => 'float',
        'max_temp' => 'float',
        'avg_temp' => 'float',
        'temp_date' => 'date',
        'updatetime' => 'datetime'
    ];
}
