<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeImageColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('weekly_tasks', function (Blueprint $table) {
            $table->text('image')->change();
            $table->text('day1_badge')->change();
            $table->text('day2_badge')->change();
            $table->text('day3_badge')->change();
            $table->text('day4_badge')->change();
            $table->text('day5_badge')->change();
            $table->text('day6_badge')->change();
            $table->text('day7_badge')->change();
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
            $table->binary('image')->change();
            $table->binary('day1_badge')->change();
            $table->binary('day2_badge')->change();
            $table->binary('day3_badge')->change();
            $table->binary('day4_badge')->change();
            $table->binary('day5_badge')->change();
            $table->binary('day6_badge')->change();
            $table->binary('day7_badge')->change();
        });
    }
}
