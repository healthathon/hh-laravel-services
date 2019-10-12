<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSHAAnswerBasedLevelRestrictionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sha__answer_level_restrictions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("sha_answer_id")->unsigned()->nullable(false)->comment("SHA Answer Ref. Id");
            $table->integer("restriction_level")->nullable(false)->comment("Restricted level");
            $table->foreign("sha_answer_id", "sha_answer_restrictions_fk")
                ->on("sha_question_answers")
                ->references("id")
                ->onDelete("cascade")
                ->onUpdate("cascade");
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
        Schema::dropIfExists('sha__answer_level_restrictions');
    }
}
