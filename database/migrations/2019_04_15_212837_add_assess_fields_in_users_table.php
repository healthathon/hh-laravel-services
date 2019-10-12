<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAssessFieldsInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('lifestyle_level')->default(0)->after('mental_level')->comment('User Reached at which level in Mental Module');
            $table->integer('nutrition_level')->default(0)->after('lifestyle_level')->comment('User Reached at which level in Nutrition Module');
            $table->integer('lifestyle_task_completed')->default(0)->after('nutrition_level')->comment('No of Task Completed in Nutrition Assess  Module');
            $table->integer('nutrition_task_completed')->default(0)->after('physical_task_completed')->comment('No of Task Completed in LifeStyle Assess  Module');
            $table->integer('lifestyle_score')->default(0)->after('mental_task_completed')->comment('Score of user in LifeStyle Module');
            $table->integer('nutrition_score')->default(0)->after('physical_score')->comment('Score of user in Nutrition Module');
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
                'lifestyle_level',
                'nutrition_level',
                'lifestyle_task_completed',
                'nutrition_task_completed',
                'lifestyle_score',
                'nutrition_score',
            ]);
        });
    }
}
