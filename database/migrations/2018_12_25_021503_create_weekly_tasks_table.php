<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWeeklyTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('weekly_tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('taskBank_id');
            $table->integer('week');
            $table->string('day1_title');
            $table->string('day2_title');
            $table->string('day3_title');
            $table->string('day4_title');
            $table->string('day5_title');
            $table->string('day6_title');
            $table->string('day7_title');



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
        Schema::dropIfExists('weekly_tasks');
    }
}
