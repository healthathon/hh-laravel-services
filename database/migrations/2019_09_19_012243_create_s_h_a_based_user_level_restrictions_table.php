<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSHABasedUserLevelRestrictionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sha__user_level_restrictions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("user_id")->unsigned()->nullable(false)->comment("User Ref. Id");
            $table->integer("restriction_level")->nullable(false)->comment("User Restricted to level");
            $table->foreign("user_id", "user_level_restrictions_fk")
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
        Schema::dropIfExists('sha__user_level_restrictions');
    }
}
