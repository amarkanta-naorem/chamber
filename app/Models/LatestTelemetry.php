<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LatestTelemetry extends Model
{
    use HasFactory;
    protected $table = 'latest_telemetry';
    
    // Specify the primary key if it's not 'id'
    protected $primaryKey = 'sys_service_id';

    // If you want to disable timestamps (created_at and updated_at)
    public $timestamps = false;

    // Specify the fillable fields if needed
    protected $fillable = [
        'sys_service_id', 
        'sys_msg_type', 
        'sys_proc_time', 
        'sys_proc_host', 
        'sys_asset_id', 
        'sys_geofence_id', 
        'sys_device_id', 
        'gps_time', 
        'gps_latitude', 
        'gps_longitude', 
        'gps_orientation', 
        'gps_speed', 
        'gps_fix', 
        'geo_street', 
        'geo_town', 
        'geo_country', 
        'geo_postcode', 
        'jny_distance', 
        'jny_duration', 
        'jny_idle_time', 
        'jny_status', 
        'jny_leg_code', 
        'jny_device_jny_id', 
        'des_movement_id', 
        'des_vehicle_id', 
        'tel_state', 
        'tel_ignition', 
        'tel_alarm', 
        'tel_panic', 
        'tel_shield', 
        'tel_theft_attempt', 
        'tel_tamper', 
        'tel_ext_alarm', 
        'tel_journey', 
        'tel_journey_status', 
        'tel_idle', 
        'tel_ex_idle', 
        'tel_hours', 
        'tel_input_0', 
        'tel_input_1', 
        'tel_input_2', 
        'tel_input_3', 
        'tel_temperature', 
        'tel_voltage', 
        'main_powervoltage', 
        'tel_odometer', 
        'tel_poweralert', 
        'tel_speedalert', 
        'tel_boxalert', 
        'tel_fuel', 
        'tel_rfid', 
        'tel_rawlog'
    ];
}
