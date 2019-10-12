<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDaysBadgeImageColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('weekly_tasks', function (Blueprint $table) {
            $table->binary('day1_badge')->after('day1_message')->nullable(true)->comment("Badge of Day 1 Task");
            $table->binary('day2_badge')->after('day2_message')->nullable(true)->comment("Badge of Day 2 Task");
            $table->binary('day3_badge')->after('day3_message')->nullable(true)->comment("Badge of Day 3 Task");
            $table->binary('day4_badge')->after('day4_message')->nullable(true)->comment("Badge of Day 4 Task");
            $table->binary('day5_badge')->after('day5_message')->nullable(true)->comment("Badge of Day 5 Task");
            $table->binary('day6_badge')->after('day6_message')->nullable(true)->comment("Badge of Day 6 Task");
            $table->binary('day7_badge')->after('day7_message')->nullable(true)->comment("Badge of Day 7 Task");
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
            $table->dropColumn([
                'day1_badge', 'day2_badge', 'day3_badge', 'day4_badge', 'day5_badge', 'day6_badge', 'day7_badge'
            ]);
        });
    }
}
