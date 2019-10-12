<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveFieldsFromUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'physics_level', 'mental_level', 'nutrition_level', 'lifestyle_level',
                'physics_task_completed', 'mental_task_completed', 'nutrition_task_completed', 'lifestyle_task_completed',
                'physics_score', 'mental_score', 'nutrition_score', 'lifestyle_score', 'overall_score'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('physics_level')->default(1)->comment("User Level in Physical Category");
            $table->integer('physics_task_completed')->default(0)->comment("Task Completed by User in Physical");
            $table->bigInteger('physics_score')->default(0)->comment("Score secured by user in Physical");
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
        });
    }
}
