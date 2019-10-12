<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSHATaskRecommendationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sha__task_recommendations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("answer_id")->unsigned()->nullable(false)->comment('SHA Answer ID');
            $table->integer("task_id")->unsigned()->nullable(false)->comment('Task/Regimen ID');
            $table->foreign("answer_id", "sha_answer_taskrecommend_fk")
                ->references('id')
                ->on('sha_question_answers')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign("task_id", "sha_task_fk")
                ->references('id')
                ->on('task_banks')
                ->onDelete('cascade')
                ->onUpdate('cascade');
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
        Schema::dropIfExists('sha__task_recommendations');
    }
}
