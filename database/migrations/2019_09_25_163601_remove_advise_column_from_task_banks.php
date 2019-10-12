<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveAdviseColumnFromTaskBanks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('task_banks', function (Blueprint $table) {
            $table->dropColumn("advise");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('task_banks', function (Blueprint $table) {
            $table->mediumText("advise")->nullable(false)->after("code")->comment("Regimen Advise");
        });
    }
}
