<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTaskRecommendationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_task_recommendations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable(true)->comment("Reference to user");
            $table->integer('regimen_id')->nullable(true)->comment("Reference to regimen");
//            $table->foreign('user_id')
//                ->references('id')
//                ->on('users')
//                ->onUpdate('cascade')
//                ->onDelete('set null');
//            $table->foreign('regimen_id')
//                ->references('id')
//                ->on('task_banks')
//                ->onUpdate('cascade')
//                ->onDelete('set null');
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
        Schema::dropIfExists('user_task_recommendations');
    }
}
