<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThyrocareTestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('thyrocare_tests', function (Blueprint $table) {
            $table->increments('id');
            $table->string('profile', 255)->nullable(true)->comment("Test belongs to which profile");
            $table->string('test_code', 20)->nullable(true)->comment("Test Code by Health Application");
            $table->string('abbr', 20)->nullable(true)->comment("Abbreviation of Test");
            $table->longText('about')->nullable(true)->comment("Details of Test");
            $table->longText('reason_to_do')->nullable(true)->comment("Why to do this test");
            $table->string('sample_type', 100)->nullable(true)->comment("Sample Type of Test");
            $table->mediumText('preparation')->nullable(true)->comment("How to prepare for test");
            $table->string('process_duration', 100)->nullable(true)->comment("Process Duration");
            $table->string('result_duration', 100)->nullable(true)->comment("Result/Reporting Duration");
            $table->longText('results')->nullable(true)->comment("Results of test");
            $table->string('age_group', 255)->nullable(true)->comment("Segemtation accodring to the age  of the pateints");
            $table->longText('good_range')->nullable(true)->comment("Good Range of test");
            $table->string('parameters_tested', 255)->nullable(true)->comment("Parameters Tested");
            $table->string('parameters_tested_unit', 255)->nullable(true)->comment("Units of Parameters Tested");
            $table->mediumInteger('price')->nullable(true)->comment("Test Price");
            $table->mediumText('test_suggestions')->nullable(true)->comment("Test Suggestions to understand the unspseicific reason of abnormaility");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('thyrocare_tests');
    }
}
