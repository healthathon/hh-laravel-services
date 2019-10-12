<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHealthAppKitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('health_app_kits', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->default(-1)->comment("User Reference Id");
            $table->string('steps', 255)->nullable(true);
            $table->boolean('sleep')->nullable(true)->default(0);
            $table->string('walk', 255)->nullable(true);
            $table->string('heart_rate')->nullable(true);
            $table->string('bmi')->nullable(true);
            $table->string('temp')->nullable(true);
            $table->string('fate')->nullable(true);
            $table->dateTime('start_date')->nullable(true);
            $table->dateTime('end_date')->nullable(true);
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
        Schema::dropIfExists('health_app_kits');
    }
}
