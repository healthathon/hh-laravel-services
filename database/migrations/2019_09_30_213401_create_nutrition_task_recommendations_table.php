<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNutritionTaskRecommendationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nutrition_task_recommendations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("nutrition_bank_id")->unsigned()->comment("Nutrition Score Reference");
            $table->integer("regimen_id")->unsigned()->comment("Regimen Id");
            $table->engine = "InnoDB";
            $table->foreign("nutrition_bank_id")
                ->references("id")
                ->on("nutrition_score_banks")
                ->onUpdate("cascade")
                ->onDelete("cascade");
            $table->foreign("regimen_id")
                ->references("id")
                ->on("task_banks")
                ->onUpdate("cascade")
                ->onDelete("cascade");
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
        Schema::dropIfExists('nutrition_task_recommendations');
    }
}
