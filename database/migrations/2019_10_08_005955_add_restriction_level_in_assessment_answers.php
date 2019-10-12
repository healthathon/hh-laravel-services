<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRestrictionLevelInAssessmentAnswers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assessment_answers', function (Blueprint $table) {
            $table->integer("restricted_level")->after("answer")->nullable(true)->comment("Restriction Level");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assessment_answers', function (Blueprint $table) {
            $table->dropColumn("restricted_level");
        });
    }
}
