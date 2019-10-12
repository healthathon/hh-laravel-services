<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThyrocareBeanDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('thyrocare_bean_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_id')->nullable(false)->comment("Order no");
            $table->string('lead_id')->nullable(false)->comment("Lead no");
            $table->string('name')->nullable(false)->comment("Bean/Patient Name");
            $table->string('gender')->nullable(false)->comment("Bean/Patient Gender");
            $table->integer('age')->nullable(false)->comment("Age of Bean/Patient");
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
        Schema::dropIfExists('thyrocare_bean_details');
    }
}
