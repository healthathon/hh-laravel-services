<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSHAQuestionAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sha_question_answers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('question_id')->unsigned()->nullable(false)->comment("Question Number Ref.");
            $table->string('answer')->nullable(false)->comment("Question Answer");
            $table->engine = 'InnoDB';
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
        Schema::dropIfExists('sha_question_answers');
    }
}
