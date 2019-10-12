<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRegimenCodeField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('task_banks', function (Blueprint $table) {
            $table->string('code', 255)->after('task_name')->nullable(false)->default("CODE")->comment("Reference Code");
            $table->mediumText('advise')->after('code')->comment("Reference Code");
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
            $table->dropColumn(['code', 'advise']);
        });
    }
}
