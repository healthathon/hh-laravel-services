<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThyrocareUserDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('thyrocare_user_datas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_id', 255)->nullable(false)->default("XXXXXXXX")->comment("Booking Order Id of User");
            $table->json('order_details')->nullable(true)->comment("Response Received from Thyrocare Server");
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
        Schema::dropIfExists('thyrocare_user_datas');
    }
}
