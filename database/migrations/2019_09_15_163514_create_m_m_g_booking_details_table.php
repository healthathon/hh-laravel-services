<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMMGBookingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mmg_booking_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("user_id")->nullable(false)->comment("Reference to User id");
            $table->integer("test_id")->nullable(false)->comment("Reference to MMG Test id");
            $table->engine = "InnoDB";
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
        Schema::dropIfExists('mmg_booking_details');
    }
}
