<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTaskTrackersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_task_trackers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_task_id')->nullable(false)->comment("Reference to user task \'id\' column");
            $table->integer('week')->nullable(false)->comment("week No");
            $table->text('days_status')->comment("Each Day Status");
            $table->boolean('week_status')->default(false);
            $table->integer('week_percentage')->comment("unit is %");
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
        Schema::dropIfExists('user_task_trackers');
    }
}
