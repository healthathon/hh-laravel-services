<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDefaultValueForSleep extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('health_app_kits', function (Blueprint $table) {
            $table->integer('sleep')->nullable(true)->default(-1)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('health_app_kits', function (Blueprint $table) {
            $table->boolean('sleep')->nullable(true)->default(0)->change();
        });
    }
}
