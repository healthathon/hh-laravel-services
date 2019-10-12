<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssessmentAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assessment_answers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tag_id')->default(0)->comment('Tag ID');
            $table->integer('query_id')->default(0)->comment('Query Question ID');
            $table->string('answer')->nullable(false)->comment('Query Question Answer');
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
        Schema::dropIfExists('assessment_answers');
    }
}
