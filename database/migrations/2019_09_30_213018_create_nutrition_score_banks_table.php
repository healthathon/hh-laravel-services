<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNutritionScoreBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nutrition_score_banks', function (Blueprint $table) {
            $table->increments('id');
            $table->string("expression")->nullable(false)->comment("Evaluation Expression");
            $table->integer("score")->nullable(false)->comment("Score");
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
        Schema::dropIfExists('nutrition_score_banks');
    }
}
