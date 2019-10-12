<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserPhysicalTaskTrackingLevelWisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_physical_task_tracking_level_wises', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("user_id")->unsigned()->nullable(false)->comment("User Id Ref.");
            $table->integer("task_completed")->default(0)->comment("# of Task Completed In Physical");
            $table->integer("level")->default(1)->comment("Level of Task");
            $table->foreign("user_id", "user_phys_task_completed_by_level_fk")
                ->on("users")
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
        Schema::dropIfExists('user_physical_task_tracking_level_wises');
    }
}
