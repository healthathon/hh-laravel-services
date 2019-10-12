<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMentalWellBeingLevelMappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mental_well_being_level_mappings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tag')->nullable(false)->comment("MH*");
            $table->string('state')->nullable(false)->comment("Good/Depressed....");
            $table->integer("level")->unsigned()->comment("Level Assigned");
            $table->integer("score")->unsigned()->comment("Score Assigned");
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
        Schema::dropIfExists('mental_well_being_level_mappings');
    }
}
