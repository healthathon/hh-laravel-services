<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTypeLabsFieldsAdded extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('diagnostic_labs_informations', function (Blueprint $table) {
            $table->text('offer_data')->change();
            $table->text('test_data')->change();
            $table->text('profile_data')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('diagnostic_labs_informations', function (Blueprint $table) {
            $table->json('offer_data')->change();
            $table->json('test_data')->change();
            $table->json('profile_data')->change();
        });
    }
}
