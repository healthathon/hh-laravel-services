<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMixedBagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mixed_bags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('regimen_name', 255)->nullable(false)->default("Regimen");
            $table->string('mapper')->nullable(false)->default("category")->comment(" This regimen is mapped under one of the available categories");
            $table->string('day_1')->nullable(false)->default("")->comment("day 1 task");
            $table->string('day_2')->nullable(false)->default("")->comment("day 2 task");
            $table->string('day_3')->nullable(false)->default("")->comment("day 3 task");
            $table->string('day_4')->nullable(false)->default("")->comment("day 4 task");
            $table->string('day_5')->nullable(false)->default("")->comment("day 5 task");
            $table->string('day_6')->nullable(false)->default("")->comment("day 6 task");
            $table->string('day_7')->nullable(false)->default("")->comment("day 7 task");
            $table->string('user_ids')->toArray();
            $table->string('user_history')->nullable(true)->comment("User task history");
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
        Schema::dropIfExists('mixed_bags');
    }
}
