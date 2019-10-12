<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsInUserAssessmentRecords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_assessment_records', function (Blueprint $table) {
            $table->integer('order_seq_id')->after('user_id')->default(1)->comment("The sequence order user is following");
            $table->string('tags_completed')->after('order_seq_id')->comment("The tags questions completed by user");
            $table->dropColumn([
                'physical_level',
                'mental_level'
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
            $table->integer('physical_level')->default(2)->after("finish_state");
            $table->integer('mental_level')->default(2)->after("physical_level");
        });
    }
}
