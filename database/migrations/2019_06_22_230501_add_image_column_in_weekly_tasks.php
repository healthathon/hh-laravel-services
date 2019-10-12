<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImageColumnInWeeklyTasks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('weekly_tasks', function (Blueprint $table) {
            $table->binary('image')->after('day7_message')->nullable(true)->comments("Image of Daily Tasks");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('weekly_tasks', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
}
