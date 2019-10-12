<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCategoryColumnTypeInTaskBank extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('task_banks', function (Blueprint $table) {
            $table->integer('category')->default(0)->nullable(false)->comment("Map id with categories table")->change();
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
            $table->string('category')->change();
        });
    }
}
