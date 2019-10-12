<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('physics_task_completed')->default(0)->after('mental_level')->comment('No of Task Completed in Physical Assess  Module');
            $table->integer('physics_score')->default(0)->after('mental_task_completed')->comment('Score of user in Physical Module');
            $table->dropColumn(['physical_score', 'physical_task_completed']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('physical_task_completed')->default(0)->after('mental_level')->comment('No of Task Completed in Physical Assess  Module');
            $table->integer('physical_score')->default(0)->after('mental_task_completed')->comment('Score of user in Physical Module');
            $table->dropColumn(['physics_task_completed', 'physics_score']);
        });
    }
}
