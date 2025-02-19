<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('latest_telemetry', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->bigInteger('sys_service_id')->unsigned();
            $table->integer('sys_msg_type')->unsigned()->nullable()->default(1);
            $table->dateTime('sys_proc_time');
            $table->string('sys_proc_host', 45);
            $table->string('sys_asset_id', 45)->nullable();
            $table->integer('sys_geofence_id')->unsigned()->nullable();
            $table->bigInteger('sys_device_id')->unsigned();
            $table->dateTime('gps_time');
            $table->float('gps_latitude', 12, 9)->default(0);
            $table->float('gps_longitude', 12, 9)->default(0);
            $table->float('gps_orientation', 12, 9)->default(0);
            $table->float('gps_speed', 12, 9)->default(0);
            $table->integer('gps_fix')->unsigned()->default(0);
            $table->text('geo_street')->nullable();
            $table->string('geo_town', 100)->nullable()->collate('latin1_bin');
            $table->string('geo_country', 100)->nullable();
            $table->string('geo_postcode', 100)->nullable();
            $table->string('jny_distance', 100)->nullable();
            $table->integer('jny_duration')->unsigned()->nullable();
            $table->integer('jny_idle_time')->unsigned()->nullable();
            $table->string('jny_status', 10)->default('0')->nullable();
            $table->integer('jny_leg_code')->nullable();
            $table->integer('jny_device_jny_id')->nullable();
            $table->integer('des_movement_id')->nullable();
            $table->integer('des_vehicle_id')->nullable();
            $table->integer('tel_state')->nullable();
            $table->boolean('tel_ignition')->nullable(); // Change here
            $table->boolean('tel_alarm')->nullable(); // Change here
            $table->boolean('tel_panic')->nullable(); // Change here
            $table->boolean('tel_shield')->nullable(); // Change here
            $table->boolean('tel_theft_attempt')->nullable(); // Change here
            $table->boolean('tel_tamper')->nullable(); // Change here
            $table->boolean('tel_ext_alarm')->nullable(); // Change here
            $table->boolean('tel_journey')->nullable(); // Change here
            $table->boolean('tel_journey_status')->nullable(); // Change here
            $table->boolean('tel_idle')->nullable(); // Change here
            $table->boolean('tel_ex_idle')->nullable(); // Change here
            $table->integer('tel_hours')->unsigned()->nullable();
            $table->boolean('tel_input_0')->nullable(); // Change here
            $table->boolean('tel_input_1')->nullable(); // Change here
            $table->boolean('tel_input_2')->nullable(); // Change here
            $table->boolean('tel_input_3')->nullable(); // Change here
            $table->float('tel_temperature', 12, 9)->nullable();
            $table->float('tel_voltage', 12, 9)->nullable();
            $table->float('main_powervoltage', 12, 9)->nullable();
            $table->bigInteger('tel_odometer')->unsigned()->nullable();
            $table->boolean('tel_poweralert')->nullable(); // Change here
            $table->boolean('tel_speedalert')->nullable(); // Change here
            $table->boolean('tel_boxalert')->nullable(); // Change here
            $table->float('tel_fuel', 12, 9)->nullable();
            $table->string('tel_rfid', 50)->nullable();
            $table->text('tel_rawlog')->nullable();

            // Indexes
            $table->primary('sys_service_id');
            $table->unique('id');
            $table->index('sys_service_id');
            $table->index(['sys_service_id', 'gps_time']);
            $table->index('geo_country');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('latest_telemetry');
    }
};
