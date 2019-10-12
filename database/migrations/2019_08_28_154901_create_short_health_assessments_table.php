<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShortHealthAssessmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('short_health_assessments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('header')->nullable(false)->comment("Main Title");
            $table->string('question')->nullable(false)->comment("Health Question");
            $table->text('answers')->nullable(false)->comment("Health Question Answers");
            $table->boolean('multiple')->default(1)->comment("Answers can be multiple");
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
        Schema::dropIfExists('short_health_assessments');
    }
}
