<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSHATestRecommendationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sha__test_recommendations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("answer_id")->unsigned()->nullable(false)->comment('SHA Answer ID');
            $table->integer("test_id")->unsigned()->nullable(false)->comment('Test/Regimen ID');
            $table->foreign("answer_id", "sha_answer_testrecommend_fk")
                ->references('id')
                ->on('sha_question_answers')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign("test_id", "sha_test_fk")
                ->references('id')
                ->on('labs_tests')
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
        Schema::dropIfExists('sha__test_recommendations');
    }
}
