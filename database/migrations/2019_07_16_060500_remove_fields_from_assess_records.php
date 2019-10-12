<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveFieldsFromAssessRecords extends Migration
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
                'categories_complete_track',
                'user_record_track'
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
            $table->json('user_record_track')->nullable(false)->after('category3_state');
            $table->json("categories_complete_track")->nullable(false)->after("tag7_state")->comment("Keep Track which categories is completed entirely by answering queries of each tags");
        });
    }
}
