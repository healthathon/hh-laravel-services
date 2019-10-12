<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRecommendedTest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('asses_histories', function (Blueprint $table) {
            $table->mediumText('recommended_test')->after('category3_state')->nullable(true)->comment("Recommended Test for User");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('asses_histories', function (Blueprint $table) {
            $table->dropColumn('recommended_test');
        });
    }
}
