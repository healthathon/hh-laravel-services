<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCategoriesInMixedBagUserHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mixed_bag_user_histories', function (Blueprint $table) {
            $table->integer('category')->default(0)->nullable(false)->after('regimen_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mixed_bag_user_histories', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
}
