<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserRegimenScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_regimen_scores', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('regimen_id')->nullable(false);
            $table->integer('user_id')->nullable(false);
            $table->integer('task_completed')->default(0)->comment("No of Task Completed by user in this regimen");
            $table->integer('secured_score')->default(0)->comment("Score secured by user in this regimen");
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
        Schema::dropIfExists('user_regimen_scores');
    }
}
