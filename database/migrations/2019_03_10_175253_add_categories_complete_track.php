<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCategoriesCompleteTrack extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('asses_histories', function (Blueprint $table) {
            $table->json("categories_complete_track")->nullable(false)->after("tag7_state")->comment("Keep Track which categories is completed entirely by answering queries of each tags");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assess_histories', function (Blueprint $table) {
            $table->dropColumn("categories_complete_track");
        });
    }
}
