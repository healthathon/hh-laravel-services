<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserHealthHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_health_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable(false)->comment('Point to id column in users table');
            $table->string('goals', 255)->nullable(false)->comment('Goals of Users');
            $table->string('overall_health', 255)->nullable(false)->comment('Overall Health of Users');
            $table->string('pre_existing_conditions', 255)->nullable(false)->comment('Existing Conditions of Users');
            $table->string('family_history', 255)->nullable(false)->comment('Family History of Users');
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
        Schema::dropIfExists('user_health_histories')->disableForeignKeyConstraints();
    }
}
