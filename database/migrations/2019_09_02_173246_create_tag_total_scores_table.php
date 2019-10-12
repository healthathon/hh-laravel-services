<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTagTotalScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('query__tags_total_scores', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tag_id')->nullable(false)->comment("Question Tag Reference id");
            $table->integer('score')->default(0)->comment("Overall Score");
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
        Schema::dropIfExists('query__tags_total_scores');
    }
}
