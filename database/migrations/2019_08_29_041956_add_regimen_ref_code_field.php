<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRegimenRefCodeField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('weekly_tasks', function (Blueprint $table) {
            $table->string("taskBank_id")->default("CODE")->change();
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
            $table->integer('taskBank_id')->default(1)->change();
        });
    }
}
