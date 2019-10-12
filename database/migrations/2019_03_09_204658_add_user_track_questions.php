<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserTrackQuestions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('asses_histories', function (Blueprint $table) {
            $table->json('user_record_track')->nullable(false)->after('category3_state');
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
            $table->dropColumn('user_record_track');
        });
    }
}
