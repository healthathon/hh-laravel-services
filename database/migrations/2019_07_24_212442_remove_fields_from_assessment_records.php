<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveFieldsFromAssessmentRecords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_assessment_records', function (Blueprint $table) {
            $table->dropColumn([
                'next_tag_id',
                'next_query_id'
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
        Schema::table('user_assessment_records', function (Blueprint $table) {
            $table->integer('next_tag_id')->nullable(false)->after('tag7_score');
            $table->integer('next_query_id')->nullable(false)->after('next_tag_id');
        });
    }
}
