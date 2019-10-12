<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTestsRecommendationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_tests_recommendations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable(true)->comment("Reference to user");
            $table->integer('test_id')->nullable(true)->comment("Reference to test");
//            $table->foreign('user_id')
//                ->references('id')
//                ->on('users')
//                ->onUpdate('cascade')
//                ->onDelete('set null');
//            $table->foreign('test_id')
//                ->references('id')
//                ->on('labs_tests')
//                ->onUpdate('cascade')
//                ->onDelete('set null');
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
        Schema::dropIfExists('users_tests_recommendations');
    }
}
