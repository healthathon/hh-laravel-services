<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('physical_task_completed')->default(0)->after('mental_level')->comment('No of Task Completed in Physical Assess  Module');
            $table->integer('mental_task_completed')->default(0)->after('physical_task_completed')->comment('No of Task Completed in Mental Assess  Module');
            $table->integer('physical_score')->default(0)->after('mental_task_completed')->comment('Score of user in Physical Module');
            $table->integer('mental_score')->default(0)->after('physical_score')->comment('Score of user in Mental Module');
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
            $table->dropColumn([
                'physical_task_completed',
                'mental_task_completed',
                'physical_score',
                'mental_score'
            ]);
        });
    }
}
