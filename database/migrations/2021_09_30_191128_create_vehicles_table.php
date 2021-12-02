<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_model_id')->constrained();
            $table->foreignId('driver_id')->constrained('users');
            $table->foreignId('vehicle_type_id')->constrained();
            $table->string('reg_no');
            $table->string('color')->default("Black");
            $table->boolean('is_active')->default(true);
            //documents
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicles');
    }
}
