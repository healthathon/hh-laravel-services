<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDoneStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mixed_bag_user_histories', function (Blueprint $table) {
            $table->boolean('isComplete')->default(false)->after('user_history');
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
            $table->dropColumn('isComplete');
        });
    }
}
