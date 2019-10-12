<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssesHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asses_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('tag1_score')->default(0);
            $table->integer('tag2_score')->default(0);
            $table->integer('tag3_score')->default(0);
            $table->integer('tag4_score')->default(0);
            $table->integer('tag5_score')->default(0);
            $table->integer('tag6_score')->default(0);
            $table->integer('tag7_score')->default(0);
            $table->integer('current_tag_id');
            $table->integer('current_query_id');
            $table->integer('finish_state')->default(0);

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
        Schema::dropIfExists('asses_histories');
    }
}
