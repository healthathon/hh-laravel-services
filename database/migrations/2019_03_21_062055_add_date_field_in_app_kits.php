<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDateFieldInAppKits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('health_app_kits', function (Blueprint $table) {
            $table->dateTime('heart_rate_date')->nullable(true)->after('heart_rate');
            $table->dateTime('bmi_date')->nullable(true)->after('bmi');
            $table->dateTime('fate_date')->nullable(true)->after('fate');
            $table->dateTime('temp_date')->nullable(true)->after('temp');
            $table->dateTime('steps_date')->nullable(true)->after('steps');
            $table->dateTime('walk_date')->nullable(true)->after('walk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('health_app_kits', function (Blueprint $table) {
            $table->dropColumn([
                'heart_rate_date',
                'bmi_date',
                'fate_date',
                'temp_date',
                'steps_date',
                'walk_date'
            ]);
        });
    }
}
