<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_banks', function (Blueprint $table) {
            $table->increments('id');
            $table->text('task_name');
            $table->integer('level')->default('0');
            $table->integer('step')->default('1');
            $table->string('detail')->nullable();
            $table->string('title');
            $table->string('category');
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
        Schema::dropIfExists('task_banks');
    }
}
