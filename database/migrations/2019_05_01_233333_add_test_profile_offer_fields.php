<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTestProfileOfferFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('diagnostic_labs_informations', function (Blueprint $table) {
            $table->json('offer_data')->nullable(true)->after('address')->comment("Thyrocare Offer Details");
            $table->json('test_data')->nullable(true)->after('offer_data')->comment("Thyrocare Test Details");
            $table->json('profile_data')->nullable(true)->after('test_data')->comment("Thyrocare Profile Details");
            $table->dropColumn('details');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('=diagnostic_labs_informations', function (Blueprint $table) {
            $table->dropColumn([
                'offer_data', 'test_data', 'profile_data'
            ]);
            $table->json('details')->nullable(true)->comment("Information Regarding Tests and many more");
        });
    }
}
