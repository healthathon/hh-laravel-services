<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestCorrespondingAssessmentQuestionsAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_corresponding_assessment_questions_answers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tag_id')->default(0)->comment('Tag ID');
            $table->integer('query_id')->default(0)->comment('Query Question ID');
            $table->string('answer')->nullable(false)->comment('Query Question Answer');
            $table->text('recommended_test')->nullable(false)->comment('Test ID Reference');
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
        Schema::dropIfExists('test_corresponding_assessment_questions_answers');
    }
}
