<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTestCorrespondingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_corresponding_assessment_answers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('answer_id')->nullable(false)->comment('Answer Id Reference');
            $table->integer('recommended_test')->nullable(false)->comment('Test ID Reference');
//            $table->foreign('answer_id')
//                ->references('id')
//                ->on('assessment_answers')
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
        Schema::dropIfExists('test_corresponding_assessment_answers');
    }
}
