<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveUserIdsHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mixed_bags', function (Blueprint $table) {
            $table->dropColumn('user_ids');
            $table->dropColumn('user_history');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mixed_bags', function (Blueprint $table) {
            $table->string('user_ids')->toArray();
            $table->string('user_history')->nullable(true)->comment("User task history");
        });
    }
}
