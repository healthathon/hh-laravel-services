<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsScoreableColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('short_health_assessments', function (Blueprint $table) {
            $table->boolean('is_scoreable')->after('multiple')->default(false)->comment("Is this category question is counted in scoring");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('short_health_assessments', function (Blueprint $table) {
            $table->dropColumn(["is_scoreable"]);
        });
    }
}
