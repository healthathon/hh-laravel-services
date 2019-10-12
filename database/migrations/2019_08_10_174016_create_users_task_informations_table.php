<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTaskInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_task_informations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable(false)->comment("Reference to user");
            $table->integer('physical_level')->default(1)->comment("User Level in Physical Category");
            $table->integer('physical_task_completed')->default(0)->comment("Task Completed by User in Physical");
            $table->bigInteger('physical_score')->default(0)->comment("Score secured by user in Physical");
            $table->integer('mental_level')->default(1)->comment("User Level in Mental Category");
            $table->integer('mental_task_completed')->comment("Task Completed by User in Mental");
            $table->bigInteger('mental_score')->default(0)->comment("Score secured by user in Mental");
            $table->integer('nutrition_level')->default(1)->comment("User Level in Nutrition Category");
            $table->integer('nutrition_task_completed')->comment("Task Completed by User in Nutrition");
            $table->bigInteger('nutrition_score')->default(0)->comment("Score secured by user in Nutrition");
            $table->integer('lifestyle_level')->default(1)->comment("User Level in Lifestyle Category");
            $table->integer('lifestyle_task_completed')->comment("Task Completed by User in Lifestyle");
            $table->bigInteger('lifestyle_score')->default(0)->comment("Score secured by user in Lifestyle");
            $table->bigInteger('overall_score')->default(0)->comment("Overall Score secured by user");
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
        Schema::dropIfExists('users_task_informations');
    }
}
