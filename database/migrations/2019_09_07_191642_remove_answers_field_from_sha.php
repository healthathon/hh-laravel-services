<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveAnswersFieldFromSha extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('short_health_assessments', function (Blueprint $table) {
            $table->dropColumn([
                'answers'
            ]);
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
            $table->mediumText('answers')->comment("Health Question Answers");
        });
    }
}
