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
        Schema::create('temperature_summary', function (Blueprint $table) {
            $table->id(); // Creates an auto-incrementing BIGINT column named 'id' with a primary key

            // No need to define primary key again
            $table->unsignedBigInteger('sys_service_id'); // BIGINT for sys_service_id
            $table->float('min_temp', 12, 9)->nullable()->default(null); // FLOAT for min_temp
            $table->float('max_temp', 12, 9)->nullable()->default(null); // FLOAT for max_temp
            $table->float('avg_temp', 12, 9)->nullable()->default(null); // FLOAT for avg_temp
            $table->date('temp_date')->nullable()->default(null); // DATE for temp_date
            $table->timestamp('updatetime')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'))->useCurrent()->onUpdate(DB::raw('CURRENT_TIMESTAMP')); // TIMESTAMP for updatetime

            // Define unique index
            $table->unique(['sys_service_id', 'temp_date'], 'sys_service_id_temp_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temperature_summary');
    }
};
