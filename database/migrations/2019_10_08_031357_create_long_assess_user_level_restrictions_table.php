<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLongAssessUserLevelRestrictionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('long_assess_user_level_restrictions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("user_id")->unsigned()->nullable(false)->comment("User Ref. Id");
            $table->integer("restriction_level")->nullable(false)->comment("User Restricted to level");
            $table->foreign("user_id", "user_long_assess_level_restrictions_fk")
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
        Schema::dropIfExists('long_assess_user_level_restrictions');
    }
}
