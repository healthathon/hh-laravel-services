<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBmiScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bmi_scores', function (Blueprint $table) {
            $table->increments('id');
            $table->string('deviation_range')->nullable(false)->comment("Range it follows");
            $table->integer('score')->default(0)->comment("Score user will get");
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
        Schema::dropIfExists('bmi_scores');
    }
}
