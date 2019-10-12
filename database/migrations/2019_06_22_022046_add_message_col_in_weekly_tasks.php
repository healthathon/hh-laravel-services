<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMessageColInWeeklyTasks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('weekly_tasks', function (Blueprint $table) {
            $table->string('day1_message')->after('day1_title')->nullable(false)->default("Congratulations")->comment("Congrats message for user");
            $table->string('day2_message')->after('day2_title')->nullable(false)->default("Congratulations")->comment("Congrats message for user");
            $table->string('day3_message')->after('day3_title')->nullable(false)->default("Congratulations")->comment("Congrats message for user");
            $table->string('day4_message')->after('day4_title')->nullable(false)->default("Congratulations")->comment("Congrats message for user");
            $table->string('day5_message')->after('day5_title')->nullable(false)->default("Congratulations")->comment("Congrats message for user");
            $table->string('day6_message')->after('day6_title')->nullable(false)->default("Congratulations")->comment("Congrats message for user");
            $table->string('day7_message')->after('day7_title')->nullable(false)->default("Congratulations")->comment("Congrats message for user");
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
                'day1_message',
                'day2_message',
                'day3_message',
                'day4_message',
                'day5_message',
                'day6_message',
                'day7_message'
            ]);
        });
    }
}
