<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdviseColumnInWeeklyTasks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('weekly_tasks', function (Blueprint $table) {
            $table->mediumText("advise")->nullable(false)->after("taskBank_id")->comment("Regimen Advise");
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
            $table->dropColumn("advise");
        });
    }
}
