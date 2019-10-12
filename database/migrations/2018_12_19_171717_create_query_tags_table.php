<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQueryTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('query_tags', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id');
            $table->string('tag_name');
            $table->integer('happy_zone_score')->nullable();
            $table->integer('work_more_score')->nullable();
            $table->integer('happy_marks')->nullable();
            $table->integer('excellent_marks')->nullable();
            $table->integer('good_marks')->nullable();
            $table->integer('bad_marks')->default(0);
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
        Schema::dropIfExists('query_tags');
    }
}
