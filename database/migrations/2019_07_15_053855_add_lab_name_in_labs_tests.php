<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLabNameInLabsTests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('labs_tests', function (Blueprint $table) {
            $table->integer('lab_id')->after('abbr')->default(1)->comment("Lab Id From Lab Information Table");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('labs_tests', function (Blueprint $table) {
            $table->dropColumn('lab_id');
        });
    }
}
