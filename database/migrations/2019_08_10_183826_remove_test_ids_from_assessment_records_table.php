<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveTestIdsFromAssessmentRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_assessment_records', function (Blueprint $table) {
            $table->dropColumn('recommended_test');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_assessment_records', function (Blueprint $table) {
            $table->string('recommended_test')->after('category3_state')->comment("Recommended Test for User");
        });
    }
}
