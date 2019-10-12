<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAssessmentAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_assessment_answers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_assess_id')->default(0)->comment("User Assessment Record Id");
            $table->integer('tag_id')->default(0)->comment("Query Tag Id defines Category of Question");
            $table->integer('query_id')->default(0)->comment("Question Number");
            $table->string('answer', 255)->nullable(true)->comment("Answer given by user");
            $table->integer('score')->default(0)->comment("Score corresponding to user answer");
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
        Schema::dropIfExists('user_assessment_answers');
    }
}
