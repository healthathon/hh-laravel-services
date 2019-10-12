<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUserTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_tasks', function (Blueprint $table) {
            $table->integer('regimen_id')->after('user_id')->nullable(false)->default(0)->comments("Regimen Id Reference");
            $table->boolean("is_regimen_completed")->after('regimen_id')->default(false)->comment("Status of Regimen");
            $table->date("register_date")->after('is_regimen_completed')->nullable(true)->comment("Day user registered for this regimen");
            $table->date("start_date")->after('register_date')->nullable(true)->comment("Day user started this regimen task");
            $table->date("last_done_date")->after('start_date')->nullable(true)->comment("Last date user did this regimen task");
            $table->dropColumn(['Physics_DoingTasks', 'Mental_DoingTasks', 'Nutrition_DoingTasks', 'Lifestyle_DoingTasks', 'task']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_tasks', function (Blueprint $table) {
            $table->dropColumn(['regimen_id']);
            $table->text('Physics_DoingTasks');
            $table->text('Mental_DoingTasks');
            $table->text('Nutrition_DoingTasks');
            $table->text('Lifestyle_DoingTasks');
        });
    }
}
